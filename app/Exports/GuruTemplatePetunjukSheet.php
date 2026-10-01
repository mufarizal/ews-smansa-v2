<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class GuruTemplatePetunjukSheet implements FromArray, WithTitle
{
    public function array(): array
    {
        return [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT GURU'],
            [''],
            ['1. Jangan mengubah nama kolom di sheet "Data Guru" (baris pertama).'],
            ['2. Baris kedua di sheet "Data Guru" adalah CONTOH — hapus atau ganti dengan data asli.'],
            ['3. Kolom nip wajib diisi angka/teks tanpa spasi, dan harus unik (belum pernah dipakai).'],
            ['4. Kolom jenis_kelamin diisi HANYA dengan huruf L atau P.'],
            ['5. Kolom no_hp dan alamat boleh dikosongkan.'],
            ['6. JANGAN menggunakan rumus (formula) di sel manapun, misal =A1 atau =CONCAT(...).'],
            ['   Ketik atau paste data sebagai teks/angka biasa (paste as value).'],
            ['7. Simpan file dalam format .xlsx, lalu upload lewat halaman Import Guru.'],
        ];
    }

    public function title(): string
    {
        return 'Petunjuk';
    }
}
