from reportlab.pdfgen import canvas
from reportlab.lib.colors import HexColor, white
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import Paragraph
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.utils import ImageReader
from pathlib import Path

BASE=Path('/home/mufarizal/projects/ews-smansa-v2')
OUT=BASE/'output/pdf/EWS-SMANSA-Project-Recap.pdf'
font='/usr/share/fonts/liberation-sans-fonts/'
pdfmetrics.registerFont(TTFont('Body',font+'LiberationSans-Regular.ttf'))
pdfmetrics.registerFont(TTFont('Bold',font+'LiberationSans-Bold.ttf'))
W,H=595.28,841.89
c=canvas.Canvas(str(OUT),pagesize=(W,H));c.setTitle('EWS SMANSA v2 | Project Recap');c.setAuthor('Project EWS SMANSA')
ink=HexColor('#26332c'); green=HexColor('#36533b'); gray=HexColor('#657065'); blue=HexColor('#2563eb'); line=HexColor('#dce2d8')
def text(t,x,y,size=11,color=ink,bold=False,width=507,leading=None):
 st=ParagraphStyle('p',fontName='Bold' if bold else 'Body',fontSize=size,leading=leading or size*1.45,textColor=color)
 p=Paragraph(t,st);_,h=p.wrap(width,800);p.drawOn(c,x,H-y-h);return y+h

def box(x,y,w,h,bg='#f2f5ee'):
 c.setFillColor(HexColor(bg));c.roundRect(x,H-y-h,w,h,9,fill=1,stroke=0)
def heading(n,title,sub):
 c.setFillColor(green);c.rect(0,H-8,W,8,fill=1,stroke=0)
 text('EWS SMANSA v2  /  PROJECT RECAP',44,26,9,green,True)
 text(f'{n:02d}',510,25,13,green,True,40)
 y=text(title,44,64,27,ink,True,507,32)
 text(sub,44,y+12,11,gray)
 c.setStrokeColor(line);c.line(44,42,W-44,42)
 text('Dokumentasi aplikasi lokal • 2 Oktober 2026',44,H-31,8,gray)
 text(str(n),530,H-31,8,gray,width=20)
def screenshot(name,x,y,w,region):
 # Page clipping only; source screenshots remain unchanged. All selected sources contain no names/NIS.
 im=ImageReader(str(BASE/'tmp/pdfs'/name));iw,ih=im.getSize();sx,sy,sw,sh=region
 scale=w/sw;h=sh*scale
 c.saveState();p=c.beginPath();p.rect(x,H-y-h,w,h);c.clipPath(p,stroke=0)
 c.drawImage(im,x-sx*scale,H-y-(ih-sy)*scale,iw*scale,ih*scale)
 c.restoreState();c.setStrokeColor(line);c.roundRect(x,H-y-h,w,h,4,fill=0,stroke=1)
 return y+h

def bullet(title,body,y):
 text(title,44,y,12,green,True);return text(body,44,y+22,10.5,ink)+16

heading(1,'Dari data sekolah\nke arah pendampingan'.replace('\n','<br/>'),'Early Warning System untuk membantu guru melihat prioritas dukungan siswa.')
y=text('EWS SMANSA v2 menghubungkan data akademik, absensi, dan perilaku dalam satu aplikasi berbasis peran. Metode <b>Simple Additive Weighting (SAW)</b> merangkum kondisi menjadi skor dan kategori; integrasi <b>AI</b> menyajikan dugaan penyebab serta saran tindak lanjut yang dapat ditinjau guru.',44,190,11)
box(44,y+20,507,68)
text('DATA → SAW → PRIORITAS → REKOMENDASI AI',60,y+33,12,green,True,475)
text('Guru BK dan wali kelas tetap memegang konteks serta keputusan pendampingan.',60,y+57,10,gray,width=470)
y=screenshot('monitoring.png',44,375,300,(16,309,697,373))
text('MONITORING KELAS',365,383,10,green,True,180)
text('Contoh tampilan kelas 10-B:<br/><b>4</b> siswa kategori Perhatian dan <b>40</b> kategori Binaan.<br/><br/>Ringkasan mengarahkan guru menuju detail siswa, bukan hanya daftar nilai.',365,410,11,ink,width=175)
text('Bukti 01 · Screenshot aplikasi yang sedang berjalan. Angka merupakan kondisi dataset saat diperiksa, bukan ukuran keberhasilan intervensi.',44,y+10,9,gray)
text('Stack terverifikasi',44,650,12,green,True)
text('Laravel/PHP · Blade (@extends/@section) · Tailwind CSS · PostgreSQL · Chart.js · Gemini API · ekspor Excel',44,674,11)
text('Fokus produk: monitoring lintas data, penentuan prioritas yang dapat dijelaskan, serta rekomendasi kontekstual.',44,723,10.5,gray)
c.showPage()

heading(2,'SAW yang bisa dijelaskan','Skor berbobot menyatukan tiga kriteria pada lingkup kelas.')
rows=[('Akademik • 25%','Rata-rata nilai tugas dan ujian berstatus selesai. Jika keduanya tersedia, rata-rata kedua kelompok digabung dengan bobot sama.'),('Absensi • 40%','Jumlah catatan absensi mapel berstatus izin, sakit, atau alpha selama semester. Ini jumlah record, bukan otomatis jumlah hari unik.'),('Perilaku • 35%','Nilai mentah = max(0, 100 - total poin negatif). Poin positif disimpan sebagai konteks; belum menambah skor SAW langsung.')]
y=151
for title,body in rows:y=bullet(title,body,y)
box(44,409,507,154)
text('Normalisasi pada kode saat diperiksa',60,423,11,green,True)
text('r akademik = nilai akademik / nilai akademik maksimum kelas<br/>r absensi = minimum absensi kelas / (absensi siswa + 1)<br/>r perilaku = nilai perilaku / nilai perilaku maksimum kelas<br/><b>V = (0,25 × r akademik) + (0,40 × r absensi) + (0,35 × r perilaku)</b>',60,448,10.5,ink,width=474,leading=21)
text('Pembagi benefit menggunakan 1 bila nilai maksimum nol. Normalisasi dan skor akhir dibulatkan hingga empat desimal.',44,575,9.5,gray)
text('Contoh anonim: Sampel A',44,618,12,green,True)
text('Snapshot 1 Oktober 2026: r akademik = 0; r absensi = 0,6129; r perilaku = 0,5000. Maka V = 0 + 0,24516 + 0,175 = <b>0,4202</b>. UI menampilkan pembulatan <b>0,42</b>.',44,642,10.5)
text('<b>Aman:</b> V ≥ 0,70 &nbsp; | &nbsp; <b>Perhatian:</b> 0,50 ≤ V &lt; 0,70<br/><b>Binaan:</b> V &lt; 0,50 → kategori Sampel A.',44,705,10.5)
text('Sumber: app/Services/SAWService.php, config/ews.php, dan snapshot database.',44,757,8.5,gray)
c.showPage()

heading(3,'AI yang memberi arah tindak lanjut','Hasil tersimpan, dapat dibaca guru, dan dikaitkan dengan data EWS.')
text('Sampel A memiliki rekomendasi bertipe guru dengan provider tersimpan <b>gemini-2.5-flash</b>: lima butir penyebab dan enam butir saran. Di bawah ini adalah sebagian saran yang benar-benar tampil pada aplikasi.',44,149,11)
y=screenshot('ai.png',44,228,507,(24,331,688,281))
text('Bukti 02 · Cuplikan empat saran AI dari browser. Area identitas tidak disertakan; isi saran tidak ditulis ulang di screenshot.',44,y+10,9,gray)
y=bullet('Konteks yang masuk ke AI','Prompt mencakup skor, kategori, akademik, ketidakhadiran, poin perilaku, dan status kelengkapan data. Respons disimpan dalam struktur penyebab dan saran.',492)
y=bullet('Dua gaya komunikasi','Kode membedakan rekomendasi bagi guru dan refleksi yang ramah bagi siswa. Halaman siswa berfokus pada saran, bukan menampilkan penyebab secara blak-blakan.',y)
y=bullet('Masukan dari guru','Form feedback tersedia agar guru dapat memberikan konteks tambahan. Mekanisme regenerasi ada pada service; proses kirim dan regenerasi tidak dijalankan dalam dokumentasi ini.',y)
text('AI menyajikan hipotesis, bukan fakta tentang kondisi keluarga atau psikologis siswa. Saran perlu ditinjau guru; hasil pendidikan setelah intervensi belum diukur.',44,748,9,gray)
c.showPage()

heading(4,'Tren, bukan hanya satu angka','Riwayat membantu guru melihat apakah kondisi berubah dari waktu ke waktu.')
y=screenshot('trend.png',44,153,507,(16,0,697,445))
text('Bukti 03 · Grafik Chart.js dari halaman siswa yang sama. Rentang terlihat pada grafik dimulai 18 September 2026; skor berada di sekitar 0,42 dan relatif datar.',44,y+12,9.5,gray)
y=bullet('Apa yang benar-benar terbukti','Grafik menampilkan riwayat snapshot tersimpan. Garis datar menunjukkan skor pada contoh ini belum berubah; tidak menjadi bukti bahwa rekomendasi AI sudah meningkatkan hasil belajar.',548)
y=bullet('Monitoring yang mendukung kerja guru','Ringkasan per kelas mengantar ke daftar prioritas Binaan → Perhatian → Aman. Detail menyatukan skor, tren, rekomendasi, dan riwayat perilaku.',y)
y=bullet('Fitur pendukung yang tersedia','Akses berdasarkan peran, export Excel, indikator data tidak lengkap, tugas, ujian harian, absensi, dan pencatatan perilaku mendukung alur monitoring. Fokus bukti browser dokumen ini pada monitoring, tren, dan AI.',y)
c.showPage()

heading(5,'Deskripsi siap untuk LinkedIn','Ringkasan singkat yang menonjolkan nilai produk tanpa klaim berlebihan.')
box(44,150,507,258)
text('EWS SMANSA v2 adalah aplikasi monitoring siswa yang menghubungkan data akademik, kehadiran, dan perilaku untuk mendukung pendampingan yang lebih terarah.<br/><br/>Metode Simple Additive Weighting (SAW) mengolah tiga kriteria menjadi skor dan kategori prioritas. Integrasi Gemini menyajikan rekomendasi kontekstual bagi guru, sementara grafik tren membantu melihat perkembangan data dari waktu ke waktu.<br/><br/>Dalam dokumentasi aplikasi, alur ini sudah terlihat dari ringkasan kelas, hasil SAW tersimpan, hingga saran AI pada detail siswa. Fokus pengembangannya adalah membuat data sekolah lebih mudah ditinjau dan ditindaklanjuti, dengan guru tetap menjadi pengambil keputusan.',60,167,11,ink,width=475)
text('#Laravel #TailwindCSS #EdTech #DecisionSupportSystem #SAW #GenerativeAI',44,428,10,blue)
text('Catatan verifikasi sebelum publikasi',44,474,14,green,True)
text('• <b>Dataset:</b> riwayat perilaku contoh memuat label “Generator”. Dokumentasi membuktikan fitur bekerja pada dataset aplikasi yang diperiksa, bukan penggunaan produksi atau dampak pada siswa nyata.<br/><br/>• <b>Label normalisasi:</b> pemetaan label normalisasi pada view belum selaras dengan field simpan SAW. Contoh hitung di halaman 2 mengikuti kode perhitungan, bukan label breakdown UI yang tertukar.<br/><br/>• <b>Batas rumus:</b> bila minimum ketidakhadiran kelas = 0, kontribusi absensi menjadi 0 untuk seluruh siswa pada rumus saat ini. Angka akademik 0 juga dapat berasal dari belum tersedianya nilai. Ini perlu diperhatikan sebelum interpretasi operasional.<br/><br/>• <b>Batas AI:</b> contoh respons menyebut “30 hari”, sedangkan sumber menghitung 30 record absensi mapel. Hipotesis penyebab dan satuan perlu divalidasi guru.',44,510,10,ink,width=507,leading=14)
text('Jejak bukti: inspeksi browser lokal 2 Oktober 2026; snapshot SAW/rekomendasi 1 Oktober 2026; SAWService, AIService, EwsSnapshotService, config/ews.php, dan view monitoring. Tidak ada data aplikasi yang diubah atau AI baru yang dipanggil.',44,740,8.5,gray)
c.save()
print(OUT)
