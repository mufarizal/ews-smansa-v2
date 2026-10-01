<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GuruTemplateExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data Guru' => new GuruTemplateDataSheet,
            'Petunjuk' => new GuruTemplatePetunjukSheet,
        ];
    }
}
