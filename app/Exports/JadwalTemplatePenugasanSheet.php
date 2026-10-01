<?php

namespace App\Exports;

use App\Models\GuruMapelKelas;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class JadwalTemplatePenugasanSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection(): Enumerable
    {
        return GuruMapelKelas::with(['guru', 'mapel', 'kelas'])
            ->get()
            ->map(fn ($p) => [
                $p->kelas->nama,
                $p->mapel->nama,
                $p->guru->nip,
                $p->guru->nama,
            ]);
    }

    public function headings(): array
    {
        return ['kelas', 'mapel', 'nip_guru', 'nama_guru (referensi saja)'];
    }

    public function title(): string
    {
        return 'Penugasan Tersedia';
    }
}
