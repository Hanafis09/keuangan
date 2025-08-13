-- Tabel pengaturan untuk menyimpan identitas klinik/instansi
CREATE TABLE pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_klinik VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_telp VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL,
    logo VARCHAR(255) DEFAULT NULL
);
