<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SiswaTemplatePetunjukSheet implements FromArray, WithTitle
{
    public function array(): array
    {
        $daftarKelas = Kelas::orderBy('tingkat')->orderBy('nama')->pluck('nama')->implode(', ');

        return [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT SISWA'],
            [''],
            ['1. Jangan mengubah nama kolom di sheet "Data Siswa" (baris pertama).'],
            ['2. Baris kedua di sheet "Data Siswa" adalah CONTOH — hapus atau ganti dengan data asli.'],
            ['3. Kolom nis wajib diisi, harus unik, dan format sel diatur sebagai TEXT (bukan Number) supaya angka 0 di depan tidak hilang.'],
            ['4. Kolom jenis_kelamin diisi HANYA dengan huruf L atau P.'],
            ['5. Kolom kelas harus diisi PERSIS sama dengan nama kelas yang sudah ada di sistem, contoh: '.($daftarKelas ?: '(belum ada kelas)')],
            ['6. Kolom kelas boleh dikosongkan jika siswa belum ditempatkan di kelas manapun.'],
            ['7. Kolom tanggal_lahir diisi format YYYY-MM-DD, contoh: 2010-05-14. Boleh dikosongkan.'],
            ['8. Kolom alamat, nama_orang_tua, no_hp_orang_tua boleh dikosongkan.'],
            ['9. JANGAN menggunakan rumus (formula) di sel manapun, misal =A1 atau =CONCAT(...).'],
            ['   Ketik atau paste data sebagai teks/angka biasa (paste as value).'],
            ['10. Simpan file dalam format .xlsx, lalu upload lewat halaman Import Siswa.'],
        ];
    }

    public function title(): string
    {
        return 'Petunjuk';
    }
}
