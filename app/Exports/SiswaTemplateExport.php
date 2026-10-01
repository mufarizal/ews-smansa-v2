<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SiswaTemplateExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data Siswa' => new SiswaTemplateDataSheet,
            'Petunjuk' => new SiswaTemplatePetunjukSheet,
        ];
    }
}
