-- Tabel user_neraca untuk login keuangan
CREATE TABLE IF NOT EXISTS user_neraca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pemasukan keuangan klinik
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

-- Tabel pengeluaran keuangan klinik
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
