<?php

namespace App\Exports;

use App\Models\GuruMapelKelas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class JadwalTemplateDataSheet implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        $contoh = GuruMapelKelas::with(['guru', 'mapel', 'kelas'])->first();

        if (! $contoh) {
            return [];
        }

        return [
            [$contoh->kelas->nama, $contoh->mapel->nama, $contoh->guru->nip, 'senin', '07:00', '08:30'],
        ];
    }

    public function headings(): array
    {
        return ['kelas', 'mapel', 'guru_nip', 'hari', 'jam_mulai', 'jam_selesai'];
    }

    public function title(): string
    {
        return 'Data Jadwal';
    }
}
