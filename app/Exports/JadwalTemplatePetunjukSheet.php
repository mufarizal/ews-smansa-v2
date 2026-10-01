<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class JadwalTemplatePetunjukSheet implements FromArray, WithTitle
{
    public function array(): array
    {
        return [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT JADWAL'],
            [''],
            ['1. Jangan mengubah nama kolom di sheet "Data Jadwal" (baris pertama).'],
            ['2. Kombinasi kelas + mapel + guru_nip HARUS SUDAH ADA di menu "Penugasan Mengajar".'],
            ['   Cek daftar kombinasi yang valid di sheet "Penugasan Tersedia".'],
            ['3. Jadwal akan otomatis masuk ke semester yang sedang aktif.'],
            ['4. Kolom hari diisi salah satu: senin, selasa, rabu, kamis, jumat, sabtu (huruf kecil).'],
            ['5. Kolom jam_mulai dan jam_selesai format 24 jam, contoh: 07:00, 13:30.'],
            ['   Format sel kolom jam sebaiknya diatur sebagai TEXT agar tidak berubah otomatis oleh Excel.'],
            ['6. Sistem akan menolak baris yang bentrok jadwal (guru atau kelas sudah ada jadwal lain di jam sama).'],
            ['7. JANGAN menggunakan rumus (formula) di sel manapun.'],
            ['8. Simpan file dalam format .xlsx, lalu upload lewat halaman Import Jadwal.'],
        ];
    }

    public function title(): string
    {
        return 'Petunjuk';
    }
}
