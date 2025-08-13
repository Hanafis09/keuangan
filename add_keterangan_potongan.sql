-- Menambahkan kolom keterangan_potongan_lain ke tabel penggajian
ALTER TABLE penggajian
ADD COLUMN keterangan_potongan_lain VARCHAR(255) DEFAULT NULL AFTER potongan_lain;
