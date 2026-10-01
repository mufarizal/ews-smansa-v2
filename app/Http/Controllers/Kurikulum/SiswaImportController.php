<?php

namespace App\Http\Controllers\Kurikulum;

use App\Exports\SiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SiswaImportController extends Controller
{
    public function form()
    {
        return view('kurikulum.siswas.import');
    }

    public function template()
    {
        return Excel::download(new SiswaTemplateExport, 'template_import_siswa.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'],
        ]);

        $import = new SiswaImport;
        Excel::import($import, $request->file('file'));

        return view('kurikulum.siswas.import-hasil', [
            'berhasil' => $import->berhasil,
            'gagal' => $import->gagal,
        ]);
    }
}
