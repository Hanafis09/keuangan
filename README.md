# Sistem Penggajihan Pegawai
CREATE BY HANAFI

Sistem penggajihan pegawai dengan fitur potongan pajak, BPJS, BPJS-TK, dan tambahan jasa pelayanan.

## Fitur Utama

- ✅ **Manajemen Data Pegawai** - CRUD data pegawai dengan gaji pokok, tunjangan jabatan, dan tunjangan makan
- ✅ **Jasa Pelayanan** - Kelola jenis jasa pelayanan dan tarifnya (ECG, USG, LAB, dll)
- ✅ **Input Jasa Pegawai** - Input jumlah jasa yang dikerjakan pegawai per bulan
- ✅ **Perhitungan Gaji Otomatis** - Hitung gaji dengan potongan pajak, BPJS Kesehatan, dan BPJS Ketenagakerjaan
- ✅ **Laporan & Slip Gaji** - Generate laporan penggajian dan cetak slip gaji individual
- ✅ **Dashboard Informatif** - Overview data pegawai, jasa pelayanan, dan statistik gaji
- ✅ **Neraca Klinik** - Overview data pemasukan, pengeluaran,kas klinik

## Komponen Perhitungan Gaji

### Pendapatan
- Gaji Pokok
- Tunjangan Jabatan  
- Tunjangan Makan
- **Jasa Pelayanan** (ditambahkan sesuai jumlah yang dikerjakan)

### Potongan
- **PPh21**: 5% dari gaji kotor
- **BPJS Kesehatan**: 1% dari gaji kotor  
- **BPJS Ketenagakerjaan**: 2% dari gaji kotor

### Rumus
```
Gaji Kotor = Gaji Pokok + Tunjangan Jabatan + Tunjangan Makan + Total Jasa Pelayanan
Total Potongan = (Gaji Kotor × 5%) + (Gaji Kotor × 1%) + (Gaji Kotor × 2%)
Gaji Bersih = Gaji Kotor - Total Potongan
```

## Instalasi

### Persyaratan Sistem
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi  
- Web Server (Apache/Nginx)
- XAMPP/WAMPP (untuk development)

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   # Clone repository (jika menggunakan Git)
   git clone [repository-url]
   
   # Atau extract file ZIP ke folder htdocs
   ```

2. **Siapkan Database**
   - Buka phpMyAdmin atau MySQL client
   - Import file `database.sql` untuk membuat database dan tabel
   - Atau jalankan manual:
   ```sql
   CREATE DATABASE sistem_gaji;
   USE sistem_gaji;
   -- Kemudian copy-paste isi database.sql
   ```

3. **Konfigurasi Database**
   - Edit file `config.php` sesuai setting database Anda:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root'); 
   define('DB_PASS', ''); // Password MySQL Anda
   define('DB_NAME', 'sistem_gaji');
   ```

4. **Akses Aplikasi**
   - Buka browser dan akses: `http://localhost/gaji pegawai/`
   - Atau sesuaikan dengan path folder Anda

## Cara Penggunaan

### 1. Kelola Data Pegawai
- Masuk ke menu **Data Pegawai**
- Tambah pegawai baru dengan mengisi:
  - NIP (Nomor Induk Pegawai)
  - Nama lengkap
  - Jabatan
  - Gaji pokok
  - Tunjangan jabatan
  - Tunjangan makan

### 2. Atur Jasa Pelayanan
- Masuk ke menu **Jasa Pelayanan**
- Tambah jenis jasa dengan:
  - Kode jasa (contoh: ECG, USG)
  - Nama jasa pelayanan
  - Tarif per unit

### 3. Input Jasa Pegawai
- Masuk ke menu **Input Jasa Pegawai**
- Pilih pegawai, jasa, periode (bulan/tahun)
- Masukkan jumlah unit yang dikerjakan
- Sistem otomatis menghitung total jasa

### 4. Generate Penggajian
- Masuk ke menu **Penggajian**
- Pilih pegawai dan periode
- Klik **Generate Gaji** untuk menghitung gaji otomatis
- Sistem akan:
  - Mengambil data gaji pokok + tunjangan
  - Menambahkan total jasa pelayanan bulan tersebut
  - Menghitung potongan pajak dan BPJS
  - Menghasilkan gaji bersih

### 5. Cetak Laporan
- Masuk ke menu **Laporan**
- Filter berdasarkan periode dan pegawai
- Lihat ringkasan penggajian
- Cetak laporan atau slip gaji individual

## Contoh Perhitungan

**Pegawai: Dr. Ahmad Wijaya**
- Gaji Pokok: Rp 8.000.000
- Tunjangan Jabatan: Rp 2.000.000  
- Tunjangan Makan: Rp 500.000
- Jasa ECG (5 unit × Rp 75.000): Rp 375.000
- **Gaji Kotor**: Rp 10.875.000

**Potongan:**
- PPh21 (5%): Rp 543.750
- BPJS Kesehatan (1%): Rp 108.750
- BPJS-TK (2%): Rp 217.500
- **Total Potongan**: Rp 870.000

**Gaji Bersih**: Rp 10.005.000

## Struktur File

```
gaji-pegawai/
├── config.php          # Konfigurasi database dan konstanta
├── database.sql        # Script database dan sample data
├── index.php          # Dashboard utama
├── pegawai.php        # Manajemen data pegawai
├── jasa_pelayanan.php # Manajemen jasa pelayanan  
├── input_jasa.php     # Input jasa pegawai
├── penggajian.php     # Generate dan kelola penggajian
├── laporan.php        # Laporan dan cetak slip gaji
└── README.md          # Dokumentasi ini
```

## Teknologi yang Digunakan

- **Backend**: PHP 7.4+ dengan PDO MySQL
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Database**: MySQL
- **Icons**: Font Awesome 6
- **Print**: CSS Print Media Queries

## Customisasi

### Mengubah Persentase Potongan
Edit file `config.php`:
```php
define('PAJAK_PPH21', 5);     // Ubah persentase PPh21
define('BPJS_KESEHATAN', 1);  // Ubah persentase BPJS Kesehatan  
define('BPJS_TK', 2);         // Ubah persentase BPJS-TK
```

### Menambah Jasa Pelayanan Baru
Masuk ke menu **Jasa Pelayanan** dan tambahkan:
- Fisioterapi, Konsultasi, Operasi, dll
- Sesuaikan kode dan tarif per institusi

## Troubleshooting

### Error Database Connection
- Pastikan MySQL server berjalan
- Cek kredensial database di `config.php`
- Pastikan database `sistem_gaji` sudah dibuat

### Data Tidak Muncul
- Pastikan sudah import `database.sql`
- Cek apakah ada error di browser console
- Pastikan file PHP bisa diakses via web server

### Print Tidak Berfungsi
- Gunakan browser modern (Chrome, Firefox, Edge)
- Pastikan tidak ada adblocker yang memblokir
- Cek setting print browser

## Lisensi

Sistem ini dibuat untuk keperluan edukasi dan dapat dimodifikasi sesuai kebutuhan.

## Kontak

Jika ada pertanyaan atau butuh bantuan pengembangan lebih lanjut, silakan hubungi developer.

---
**© 2025 Sistem Penggajihan Pegawai - Versi 1.0**
