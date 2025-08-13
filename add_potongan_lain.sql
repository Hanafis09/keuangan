-- Menambahkan kolom potongan_lain ke tabel penggajian
ALTER TABLE penggajian
ADD COLUMN potongan_lain DECIMAL(12,2) DEFAULT 0 AFTER potongan_bpjs_tk;
