<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GuruTemplateDataSheet implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [
            ['197001012000011001', 'Budi Santoso', 'L', '081234567890', 'Jl. Contoh No. 1'],
        ];
    }

    public function headings(): array
    {
        return ['nip', 'nama', 'jenis_kelamin', 'no_hp', 'alamat'];
    }

    public function title(): string
    {
        return 'Data Guru';
    }
}
