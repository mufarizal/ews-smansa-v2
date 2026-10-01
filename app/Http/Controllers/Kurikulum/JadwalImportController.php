<?php

namespace App\Http\Controllers\Kurikulum;

use App\Exports\JadwalTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\JadwalImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class JadwalImportController extends Controller
{
    public function form()
    {
        return view('kurikulum.jadwals.import');
    }

    public function template()
    {
        return Excel::download(new JadwalTemplateExport, 'template_import_jadwal.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'],
        ]);

        $import = new JadwalImport;
        Excel::import($import, $request->file('file'));

        return view('kurikulum.jadwals.import-hasil', [
            'berhasil' => $import->berhasil,
            'gagal' => $import->gagal,
        ]);
    }
}
