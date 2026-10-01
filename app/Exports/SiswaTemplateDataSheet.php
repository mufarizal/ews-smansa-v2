<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SiswaTemplateDataSheet implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        $contohKelas = Kelas::first()?->nama ?? 'X-A';

        return [
            ['2025001', 'Andi Pratama', 'L', $contohKelas, '2010-05-14', 'Jl. Contoh No. 2', 'Bapak Contoh', '081234567891'],
        ];
    }

    public function headings(): array
    {
        return ['nis', 'nama', 'jenis_kelamin', 'kelas', 'tanggal_lahir', 'alamat', 'nama_orang_tua', 'no_hp_orang_tua'];
    }

    public function title(): string
    {
        return 'Data Siswa';
    }
}
