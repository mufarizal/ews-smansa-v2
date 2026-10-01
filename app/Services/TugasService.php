<?php

namespace App\Services;

use App\Models\JawabanTugas;
use App\Models\NilaiTugas;
use App\Models\Siswa;
use App\Models\SoalTugas;
use App\Models\Tugas;

class TugasService
{
    public function pastikanNilaiTugas(Tugas $tugas, Siswa $siswa)
    {
        return NilaiTugas::firstOrCreate(
            ['tugas_id' => $tugas->id, 'siswa_id' => $siswa->id],
            ['status' => 'belum_dikerjakan'],
        );
    }

    public function simpanJawaban(SoalTugas $soal, Siswa $siswa, array $payload)
    {
        $data = match ($soal->tipe_soal) {
            'pilihan_ganda' => [
                'jawaban_text' => strtoupper($payload['jawaban_text']),
                'poin_diperoleh' => strtoupper($payload['jawaban_text']) === strtoupper($soal->kunci_jawaban) ?
                    $soal->poin : 0,
            ],
            'esai' => [
                'jawaban_text' => $payload['jawaban_text'],
                'poin_diperoleh' => null,
            ],
            'upload_file' => [
                'file_path' => $payload['file_path'],
                'poin_diperoleh' => null,
            ]
        };

        $jawaban = JawabanTugas::updateOrCreate(
            ['soal_tugas_id' => $soal->id, 'siswa_id' => $siswa->id],
            $data
        );

        $nilaiTugas = $this->pastikanNilaiTugas($soal->tugas, $siswa);
        if ($nilaiTugas->status === 'belum_dikerjakan') {
            $nilaiTugas->update(['status' => 'sedang_dikerjakan']);
        }

        return $jawaban;
    }

    public function submitAkhir(Tugas $tugas, Siswa $siswa)
    {
        $nilaiTugas = $this->pastikanNilaiTugas($tugas, $siswa);
        $nilaiTugas->update(['status' => 'selesai']);
        $this->hitungUlangNilai($tugas, $siswa);
    }

    public function hitungUlangNilai(Tugas $tugas, Siswa $siswa)
    {
        $soalList = $tugas->soalTugas;
        $jawabanList = JawabanTugas::whereIn('soal_tugas_id', $soalList->pluck('id'))->where('siswa_id', $siswa->id)->get()->keyBy('soal_tugas_id');
        $totalPoin = $soalList->sum('poin');
        $totalDiperoleh = 0;
        $adaBelumDinilai = false;

        foreach ($soalList as $soal) {
            $jawaban = $jawabanList->get($soal->id);

            if (! $jawaban || $jawaban->poin_diperoleh === null) {
                $adaBelumDinilai = true;

                continue;
            }
            $totalDiperoleh += $jawaban->poin_diperoleh;
        }

        $nilaiTugas = $this->pastikanNilaiTugas($tugas, $siswa);

        if ($adaBelumDinilai) {
            $nilaiTugas->update(['status' => 'menunggu_penilaian', 'nilai' => null]);

            return;
        }

        $nilai = $totalPoin > 0 ? round(($totalDiperoleh / $totalPoin) * 100, 2) : 0;
        $nilaiTugas->update(['status' => 'selesai', 'nilai' => $nilai]);
    }
}
