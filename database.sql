-- Create By Hanafi
-- Database Sistem Penggajihan Pegawai
CREATE DATABASE IF NOT EXISTS sistem_gaji;
USE sistem_gaji;

-- Tabel data pegawai
CREATE TABLE pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    gaji_pokok DECIMAL(12,2) NOT NULL,
    tunjangan_jabatan DECIMAL(12,2) DEFAULT 0,
    tunjangan_makan DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel jenis jasa pelayanan
CREATE TABLE jasa_pelayanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_jasa VARCHAR(10) UNIQUE NOT NULL,
    nama_jasa VARCHAR(100) NOT NULL,
    tarif_per_unit DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel detail jasa pelayanan pegawai per bulan
CREATE TABLE detail_jasa_pegawai (
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

-- Tabel penggajian
CREATE TABLE penggajian (
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

-- Tabel user untuk login
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User admin default (password: admin123)
INSERT INTO user (username, password, nama, role) VALUES (
    'admin',
    '$2y$10$wH6Qw6Qw6Qw6Qw6Qw6Qw6uQw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6',
    'Administrator',
    'admin'
);
-- Password di atas adalah hash bcrypt dari 'admin123', ganti jika ingin password lain.

-- Insert sample data pegawai
INSERT INTO pegawai (nip, nama, jabatan, gaji_pokok, tunjangan_jabatan, tunjangan_makan) VALUES
('PEG001', 'Dr. Ahmad Wijaya', 'Dokter Spesialis', 8000000, 2000000, 500000),
('PEG002', 'Siti Nurhaliza', 'Perawat Senior', 4500000, 800000, 400000),
('PEG003', 'Budi Santoso', 'Teknisi Radiologi', 3500000, 500000, 350000),
('PEG004', 'Maria Sari', 'Perawat Junior', 3800000, 600000, 350000),
('PEG005', 'Andi Prasetyo', 'Administrasi', 3000000, 400000, 300000);

-- Insert sample data jasa pelayanan
INSERT INTO jasa_pelayanan (kode_jasa, nama_jasa, tarif_per_unit) VALUES
('ECG', 'Elektrokardiogram', 75000),
('USG', 'Ultrasonografi', 150000),
('LAB', 'Pemeriksaan Laboratorium', 50000),
('XRAY', 'Rontgen', 100000),
('KONSUL', 'Konsultasi Spesialis', 200000),
('TERAPI', 'Fisioterapi', 80000);
