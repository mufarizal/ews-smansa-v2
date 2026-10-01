<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JadwalTemplateExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data Jadwal' => new JadwalTemplateDataSheet,
            'Penugasan Tersedia' => new JadwalTemplatePenugasanSheet,
            'Petunjuk' => new JadwalTemplatePetunjukSheet,
        ];
    }
}
