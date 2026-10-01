<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow
{
    public int $berhasil = 0;

    /** @var array<int, array{baris: int, pesan: string}> */
    public array $gagal = [];

    private array $nipDalamFileIni = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $nomorBaris = $index + 2; // +2: baris 1 = heading, index mulai dari 0

            $data = [
                'nip' => $this->bersihkan($row['nip'] ?? null),
                'nama' => $this->bersihkan($row['nama'] ?? null),
                'jenis_kelamin' => strtoupper($this->bersihkan($row['jenis_kelamin'] ?? null)),
                'no_hp' => $this->bersihkan($row['no_hp'] ?? null),
                'alamat' => $this->bersihkan($row['alamat'] ?? null),
            ];

            // baris kosong total dilewati saja (biasanya sisa baris kosong di bawah)
            if (empty($data['nip']) && empty($data['nama'])) {
                continue;
            }

            $validator = Validator::make($data, [
                'nip' => ['required', 'string', 'max:30', 'unique:gurus,nip'],
                'nama' => ['required', 'string', 'max:150'],
                'jenis_kelamin' => ['required', 'in:L,P'],
                'no_hp' => ['nullable', 'string', 'max:20'],
                'alamat' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $this->gagal[] = [
                    'baris' => $nomorBaris,
                    'pesan' => implode(' ', $validator->errors()->all()),
                ];

                continue;
            }

            if (in_array($data['nip'], $this->nipDalamFileIni)) {
                $this->gagal[] = [
                    'baris' => $nomorBaris,
                    'pesan' => "NIP {$data['nip']} duplikat di dalam file ini.",
                ];

                continue;
            }

            $this->nipDalamFileIni[] = $data['nip'];

            try {
                DB::transaction(function () use ($data) {
                    $email = $data['nip'].'@'.config('school.email_domain_guru');

                    $user = User::create([
                        'name' => $data['nama'],
                        'email' => $email,
                        'password' => Hash::make(config('school.default_password')),
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]);

                    Guru::create([...$data, 'user_id' => $user->id]);
                });

                $this->berhasil++;
            } catch (\Throwable $e) {
                $this->gagal[] = [
                    'baris' => $nomorBaris,
                    'pesan' => 'Gagal disimpan ke database: '.$e->getMessage(),
                ];
            }
        }
    }

    /**
     * Bersihkan nilai sel dari sisa rumus/karakter tak terlihat yang sering
     * muncul kalau user paste dari sumber lain atau salah pakai formula di Excel.
     */
    private function bersihkan(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value); // hapus karakter kontrol tak terlihat
        $value = ltrim($value, "='\""); // jaga-jaga sisa awalan formula/quote
        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
