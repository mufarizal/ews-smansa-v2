<?php

namespace App\Services;

use App\Models\AiRecommendation;
use App\Models\AiRecommendationFeedback;
use App\Models\EarlyWarningResult;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private const SYSTEM_PROMPT_GURU = <<<'PROMPT'
Kamu adalah asisten pendidikan yang membantu guru BK dan wali kelas SMA menganalisis
kondisi siswa berdasarkan data akademik, kehadiran, dan perilaku. Berikan analisis yang
objektif, suportif, dan actionable — bukan menghakimi siswa. Fokus pada solusi konkret
yang bisa dilakukan guru.

Balas HANYA dalam format JSON valid, tanpa teks lain, tanpa markdown code fence, dengan struktur:
{"penyebab": ["poin 1", "poin 2"], "saran": ["saran 1", "saran 2"]}
PROMPT;

    private const SYSTEM_PROMPT_SISWA = <<<'PROMPT'
Kamu adalah asisten belajar yang membantu siswa SMA merefleksikan kondisi belajarnya
sendiri berdasarkan data akademik, kehadiran, dan perilaku. Gunakan bahasa yang ramah,
memotivasi, dan mudah dipahami siswa — hindari nada menggurui atau menghakimi.

Balas HANYA dalam format JSON valid, tanpa teks lain, tanpa markdown code fence, dengan struktur:
{"penyebab": ["poin 1", "poin 2"], "saran": ["saran 1", "saran 2"]}
PROMPT;

    private const SYSTEM_PROMPT_FEEDBACK = <<<'PROMPT'
Kamu adalah asisten pendidikan. Guru memberi masukan terhadap analisis AI sebelumnya
karena dirasa kurang tepat. Perbaiki analisis berdasarkan masukan guru tersebut.

Balas HANYA dalam format JSON valid, tanpa teks lain, tanpa markdown code fence, dengan struktur:
{"penyebab": ["poin 1", "poin 2"], "saran": ["saran 1", "saran 2"]}
PROMPT;

    public function generateHarian(?string $tanggalHitung = null): void
    {
        $tanggalHitung ??= now()->toDateString();

        Kelas::all()->each(function (Kelas $kelas) use ($tanggalHitung) {
            $this->generateUntukKelas($kelas->id, $tanggalHitung);
        });

        Siswa::whereNotNull('kelas_id')->each(function (Siswa $siswa) use ($tanggalHitung) {
            $this->generateUntukSiswa($siswa->id, $tanggalHitung);
        });
    }

    /**
     * Generate rekomendasi versi Guru BK/Wali Kelas untuk semua siswa di 1 kelas.
     * Urutan prioritas: binaan dulu, baru perhatian, baru aman.
     */
    public function generateUntukKelas(int $kelasId, ?string $tanggalHitung = null): void
    {
        $tanggalHitung ??= now()->toDateString();

        $hasilEwsList = EarlyWarningResult::with('siswa')
            ->where('kelas_id', $kelasId)
            ->where('tanggal_hitung', $tanggalHitung)
            ->orderByRaw("CASE kategori WHEN 'binaan' THEN 1 WHEN 'perhatian' THEN 2 WHEN 'aman' THEN 3 END")
            ->get();

        foreach ($hasilEwsList as $ews) {
            $this->generateSatuRekomendasi($ews, 'guru');
        }
    }

    /**
     * Generate rekomendasi versi self-reflection untuk 1 siswa.
     */
    public function generateUntukSiswa(int $siswaId, ?string $tanggalHitung = null): void
    {
        $tanggalHitung ??= now()->toDateString();

        $ews = EarlyWarningResult::with('siswa')
            ->where('siswa_id', $siswaId)
            ->where('tanggal_hitung', $tanggalHitung)
            ->first();

        if (! $ews) {
            return;
        }

        $this->generateSatuRekomendasi($ews, 'siswa');
    }

    private function generateSatuRekomendasi(EarlyWarningResult $ews, string $tipe): ?AiRecommendation
    {
        $sudahAda = AiRecommendation::where('siswa_id', $ews->siswa_id)
            ->where('tanggal_hitung', $ews->tanggal_hitung)
            ->where('tipe', $tipe)
            ->exists();

        if ($sudahAda) {
            return null;
        }

        $systemPrompt = $tipe === 'guru' ? self::SYSTEM_PROMPT_GURU : self::SYSTEM_PROMPT_SISWA;
        $userPrompt = $this->buildPrompt($ews);

        try {
            [$raw, $providerUsed] = $this->callDenganFallback($systemPrompt, $userPrompt);
            $parsed = $this->parseResponse($raw);
        } catch (\Throwable $e) {
            Log::error('AI generate gagal untuk siswa '.$ews->siswa_id.': '.$e->getMessage());

            return null;
        }

        return $this->simpanRekomendasi($ews, $tipe, $parsed, $providerUsed);
    }

    public function regenerateDariFeedback(int $feedbackId): void
    {
        $feedback = AiRecommendationFeedback::with('aiRecommendation.siswa')->findOrFail($feedbackId);
        $rekomendasiLama = $feedback->aiRecommendation;

        $feedback->update(['status' => 'diproses']);

        $prompt = $this->buildFeedbackPrompt($rekomendasiLama, $feedback);

        try {
            [$raw, $providerUsed] = $this->callDenganFallback(self::SYSTEM_PROMPT_FEEDBACK, $prompt);
            $parsed = $this->parseResponse($raw);
        } catch (\Throwable $e) {
            Log::error('AI regenerate gagal untuk feedback '.$feedbackId.': '.$e->getMessage());
            $feedback->update(['status' => 'menunggu']); // balikin biar dicoba lagi nanti

            return;
        }

        $rekomendasiLama->update([
            'penyebab' => $parsed['penyebab'],
            'saran' => $parsed['saran'],
            'provider_used' => $providerUsed,
        ]);

        $feedback->update(['status' => 'selesai']);
    }

    public function regeneratePendingFeedback(): void
    {
        AiRecommendationFeedback::where('status', 'menunggu')
            ->pluck('id')
            ->each(fn (int $id) => $this->regenerateDariFeedback($id));
    }

    private function buildPrompt(EarlyWarningResult $ews): string
    {
        $siswa = $ews->siswa;

        return <<<PROMPT
Data siswa:
- Nama: {$siswa->nama}
- NIS: {$siswa->nis}
- Kategori Early Warning: {$ews->kategori}
- Skor akhir: {$ews->skor_akhir}
- Nilai akademik (rata-rata tugas & ujian): {$ews->c1_akademik}
- Jumlah tidak hadir (izin/sakit/alpha) semester ini: {$ews->c2_absensi}
- Total poin pelanggaran perilaku: {$ews->total_perilaku_negatif}
- Total poin perilaku positif tercatat: {$ews->total_perilaku_positif}
- Data lengkap tersedia: {$this->boolToText(! $ews->data_tidak_lengkap)}

Analisis kondisi siswa ini dan berikan penyebab kemungkinan serta saran tindak lanjut.
PROMPT;
    }

    private function buildFeedbackPrompt(AiRecommendation $rekomendasiLama, AiRecommendationFeedback $feedback): string
    {
        $penyebabLama = implode('; ', $rekomendasiLama->penyebab);
        $saranLama = implode('; ', $rekomendasiLama->saran);

        return <<<PROMPT
Analisis sebelumnya untuk siswa {$rekomendasiLama->siswa->nama}:
Penyebab: {$penyebabLama}
Saran: {$saranLama}

Masukan dari guru: {$feedback->catatan}

Perbaiki analisis di atas berdasarkan masukan guru tersebut.
PROMPT;
    }

    private function boolToText(bool $value): string
    {
        return $value ? 'ya' : 'tidak, beberapa data belum lengkap';
    }

    /**
     * Panggil provider utama, kalau gagal (quota habis/rate limit/model unavailable) coba fallback.
     *
     * @return array{0: string, 1: string} [rawContent, providerModelYangDipakai]
     */
    private function callDenganFallback(string $systemPrompt, string $userPrompt): array
    {
        try {
            $raw = $this->callProvider(config('ai.primary_model'), $systemPrompt, $userPrompt);

            return [$raw, config('ai.primary_model')];
        } catch (\Throwable $e) {
            Log::warning('Model primary gagal: '.$e->getMessage().'. Mencoba fallback.');

            $raw = $this->callProvider(config('ai.fallback_model'), $systemPrompt, $userPrompt);

            return [$raw, config('ai.fallback_model')];
        }
    }

    private function callProvider(string $model, string $systemPrompt, string $userPrompt): string
    {
        $apiKey = config('ai.api_key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            throw new \RuntimeException('GEMINI_API_KEY belum dikonfigurasi.');
        }

        $response = Http::connectTimeout(5)
            ->timeout(config('ai.timeout'))
            ->withQueryParameters(['key' => $apiKey])
            ->post(
                rtrim(config('ai.base_url'), '/').'/models/'.rawurlencode($model).':generateContent',
                [
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemPrompt],
                        ],
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $userPrompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.2,
                    ],
                ],
            );

        if ($response->failed()) {
            throw new \RuntimeException("Gemini {$model} gagal: HTTP {$response->status()} - {$response->body()}");
        }

        $content = $response->json('candidates.0.content.parts.0.text');

        if (! $content) {
            $blockReason = $response->json('promptFeedback.blockReason');

            throw new \RuntimeException(
                'Gemini tidak mengembalikan konten yang valid.'
                .($blockReason ? " Alasan: {$blockReason}." : '')
            );
        }

        return $content;
    }

    /**
     * @return array{penyebab: array<string>, saran: array<string>}
     */
    private function parseResponse(string $raw): array
    {
        $bersih = trim($raw);
        $bersih = preg_replace('/^```json\s*|\s*```$/m', '', $bersih);
        $bersih = trim($bersih);

        $decoded = json_decode($bersih, true);

        if (! is_array($decoded) || ! isset($decoded['penyebab']) || ! isset($decoded['saran'])) {
            throw new \RuntimeException('Respons AI tidak sesuai format JSON yang diharapkan: '.$raw);
        }

        return [
            'penyebab' => array_values((array) $decoded['penyebab']),
            'saran' => array_values((array) $decoded['saran']),
        ];
    }

    private function simpanRekomendasi(EarlyWarningResult $ews, string $tipe, array $parsed, string $providerUsed): AiRecommendation
    {
        return AiRecommendation::updateOrCreate(
            [
                'siswa_id' => $ews->siswa_id,
                'tanggal_hitung' => $ews->tanggal_hitung,
                'tipe' => $tipe,
            ],
            [
                'kelas_id' => $ews->kelas_id,
                'semester_id' => $ews->semester_id,
                'kategori' => $ews->kategori,
                'penyebab' => $parsed['penyebab'],
                'saran' => $parsed['saran'],
                'provider_used' => $providerUsed,
            ]
        );
    }
}
