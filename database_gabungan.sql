-- Backup & Merge Database Klinik
-- Gabungan struktur dari database.sql, keuangan.sql, hrd.sql, pengaturan.sql

CREATE DATABASE IF NOT EXISTS sistem_gaji;
USE sistem_gaji;

-- Tabel Pegawai
CREATE TABLE IF NOT EXISTS pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    gaji_pokok DECIMAL(12,2) NOT NULL,
    tunjangan_jabatan DECIMAL(12,2) DEFAULT 0,
    tunjangan_makan DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Jasa Pelayanan
CREATE TABLE IF NOT EXISTS jasa_pelayanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_jasa VARCHAR(10) UNIQUE NOT NULL,
    nama_jasa VARCHAR(100) NOT NULL,
    tarif_per_unit DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Detail Jasa Pegawai
CREATE TABLE IF NOT EXISTS detail_jasa_pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT,
    jasa_id INT,
    bulan INT NOT NULL,
    tahun INT NOT NULL,
    jumlah_unit INT DEFAULT 0,
    total_jasa DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pegawai_id) REFERENCES pegawai(id) ON DELETE CASCADE,
    FOREIGN KEY (jasa_id) REFERENCES jasa_pelayanan(id) ON DELETE CASCADE
);

-- Tabel Penggajian
CREATE TABLE IF NOT EXISTS penggajian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT,
    bulan INT NOT NULL,
    tahun INT NOT NULL,
    gaji_pokok DECIMAL(12,2) NOT NULL,
    tunjangan_jabatan DECIMAL(12,2) DEFAULT 0,
    tunjangan_makan DECIMAL(12,2) DEFAULT 0,
    total_jasa_pelayanan DECIMAL(12,2) DEFAULT 0,
    gross_salary DECIMAL(12,2) NOT NULL,
    potongan_pajak DECIMAL(12,2) DEFAULT 0,
    potongan_bpjs_kesehatan DECIMAL(12,2) DEFAULT 0,
    potongan_bpjs_tk DECIMAL(12,2) DEFAULT 0,
    total_potongan DECIMAL(12,2) DEFAULT 0,
    gaji_bersih DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pegawai_id) REFERENCES pegawai(id) ON DELETE CASCADE
);

-- Tabel User Login
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel HRD
CREATE TABLE IF NOT EXISTS hrd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    kontak VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL
);

-- Tabel User Neraca (Keuangan)
CREATE TABLE IF NOT EXISTS user_neraca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pemasukan Keuangan
CREATE TABLE IF NOT EXISTS pemasukan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255),
    jumlah DECIMAL(15,2) NOT NULL,
    status_invoice VARCHAR(20) DEFAULT 'Lunas',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pengeluaran Keuangan
CREATE TABLE IF NOT EXISTS pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255),
    jumlah DECIMAL(15,2) NOT NULL,
    status_invoice VARCHAR(20) DEFAULT 'Lunas',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pengaturan Klinik
CREATE TABLE IF NOT EXISTS pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_klinik VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_telp VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL,
    logo VARCHAR(255) DEFAULT NULL
);

-- Anda bisa menambahkan data awal sesuai kebutuhan di bawah ini
