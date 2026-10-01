<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Services\EwsSnapshotService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WaliKelasSiswaExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Kelas $kelas,
        private EwsSnapshotService $ewsSnapshot,
    ) {}

    public function collection(): Collection
    {
        return $this->ewsSnapshot->siswaUrutPrioritas($this->kelas)->map(function ($siswa) {
            $ews = $siswa->ews_terkini;

            return [
                $siswa->nis,
                $siswa->nama,
                $ews?->kategori ?? 'belum ada data',
                $ews?->skor_akhir ?? '-',
                $ews?->tanggal_hitung?->format('d-m-Y') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['NIS', 'Nama', 'Kategori', 'Skor Akhir', 'Tanggal Hitung'];
    }
}
