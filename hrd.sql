-- Tabel HRD untuk menyimpan data HRD
CREATE TABLE hrd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    kontak VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL
);
