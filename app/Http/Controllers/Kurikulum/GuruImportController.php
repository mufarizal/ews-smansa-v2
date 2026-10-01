<?php

namespace App\Http\Controllers\Kurikulum;

use App\Exports\GuruTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GuruImportController extends Controller
{
    public function form()
    {
        return view('kurikulum.gurus.import');
    }

    public function template()
    {
        return Excel::download(new GuruTemplateExport, 'template_import_guru.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'],
        ]);

        $import = new GuruImport;
        Excel::import($import, $request->file('file'));

        return view('kurikulum.gurus.import-hasil', [
            'berhasil' => $import->berhasil,
            'gagal' => $import->gagal,
        ]);
    }
}
