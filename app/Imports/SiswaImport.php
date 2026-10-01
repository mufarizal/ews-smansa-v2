<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public int $berhasil = 0;

    /** @var array<int, array{baris: int, pesan: string}> */
    public array $gagal = [];

    private ?int $roleSiswaId = null;

    private array $nisDalamFileIni = [];

    /** @var Collection<string, int> nama kelas (lowercase) => id */
    private Collection $petaKelas;

    public function __construct()
    {
        $this->petaKelas = Kelas::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [strtolower(trim($nama)) => $id]);
        $this->roleSiswaId = Role::where('name', 'siswa')->value('id');
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $nomorBaris = $index + 2;

            $namaKelas = $this->bersihkan($row['kelas'] ?? null);
            $kelasId = null;

            if ($namaKelas !== null) {
                $kelasId = $this->petaKelas->get(strtolower($namaKelas));

                if ($kelasId === null) {
                    $this->gagal[] = [
                        'baris' => $nomorBaris,
                        'pesan' => "Kelas \"{$namaKelas}\" tidak ditemukan di sistem. Periksa ejaan atau buat kelas ini dulu.",
                    ];

                    continue;
                }
            }

            $data = [
                'nis' => $this->bersihkan($row['nis'] ?? null),
                'nama' => $this->bersihkan($row['nama'] ?? null),
                'jenis_kelamin' => strtoupper($this->bersihkan($row['jenis_kelamin'] ?? null) ?? ''),
                'kelas_id' => $kelasId,
                'tanggal_lahir' => $this->bersihkan($row['tanggal_lahir'] ?? null),
                'alamat' => $this->bersihkan($row['alamat'] ?? null),
                'nama_orang_tua' => $this->bersihkan($row['nama_orang_tua'] ?? null),
                'no_hp_orang_tua' => $this->bersihkan($row['no_hp_orang_tua'] ?? null),
            ];

            if (empty($data['nis']) && empty($data['nama'])) {
                continue;
            }

            $validator = Validator::make($data, [
                'nis' => ['required', 'string', 'max:30', 'unique:siswas,nis'],
                'nama' => ['required', 'string', 'max:150'],
                'jenis_kelamin' => ['required', 'in:L,P'],
                'kelas_id' => ['nullable', 'exists:kelas,id'],
                'tanggal_lahir' => ['nullable', 'date'],
                'alamat' => ['nullable', 'string'],
                'nama_orang_tua' => ['nullable', 'string', 'max:150'],
                'no_hp_orang_tua' => ['nullable', 'string', 'max:20'],
            ]);

            if ($validator->fails()) {
                $this->gagal[] = [
                    'baris' => $nomorBaris,
                    'pesan' => implode(' ', $validator->errors()->all()),
                ];

                continue;
            }

            if (in_array($data['nis'], $this->nisDalamFileIni)) {
                $this->gagal[] = [
                    'baris' => $nomorBaris,
                    'pesan' => "NIS {$data['nis']} duplikat di dalam file ini.",
                ];

                continue;
            }

            $this->nisDalamFileIni[] = $data['nis'];

            try {
                DB::transaction(function () use ($data) {
                    $email = $data['nis'].'@'.config('school.email_domain_siswa');

                    $user = User::create([
                        'name' => $data['nama'],
                        'email' => $email,
                        'password' => Hash::make(config('school.default_password')),
                        'is_active' => true,
                        'default_role_id' => $this->roleSiswaId,
                        'email_verified_at' => now(),
                    ]);

                    $user->roles()->attach($this->roleSiswaId);

                    Siswa::create([...$data, 'user_id' => $user->id]);
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

    private function bersihkan(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        $value = ltrim($value, "='\"");
        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
