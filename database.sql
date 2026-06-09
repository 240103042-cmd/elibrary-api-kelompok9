-- ============================================
-- TABEL E-Library API - Kelompok 9
-- Anggota:
--   1. Burhan Yusuf Arifin    (240103042)
--   2. Arjuna Dwi Refa S      (240103048)
-- Domain: pemrogmobile.infinityfree.me
--
-- CATATAN: Jalankan di dalam database InfinityFree
-- yang sudah ada (if0_41598099_elibrary).
-- Jangan buat database baru — sudah dibuat otomatis.
-- ============================================

-- ─────────────────────────────────────────────
-- 1. DATA KATEGORI (CATEGORY)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori`   INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(50)  NOT NULL,
  `deskripsi`     TEXT,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
-- 2. DATA ANGGOTA (MEMBER)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `anggota` (
  `id_anggota`      INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_lengkap`    VARCHAR(100) NOT NULL,
  `email`           VARCHAR(50)  NOT NULL UNIQUE,
  `no_telepon`      VARCHAR(15),
  `tanggal_daftar`  DATE         NOT NULL DEFAULT '2000-01-01',
  PRIMARY KEY (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
-- 3. DATA BUKU (BOOK)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `buku` (
  `id_buku`      INT(11)      NOT NULL AUTO_INCREMENT,
  `id_kategori`  INT(11)      NOT NULL,
  `judul_buku`   VARCHAR(150) NOT NULL,
  `penulis`      VARCHAR(100) NOT NULL,
  `penerbit`     VARCHAR(100),
  `tahun_terbit` INT(4),
  `stok`         INT(3)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_buku`),
  CONSTRAINT `fk_buku_kategori`
    FOREIGN KEY (`id_kategori`) REFERENCES `kategori`(`id_kategori`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
-- 4. DATA PEMINJAMAN (BORROWING)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `peminjaman` (
  `id_pinjam`          INT(11)     NOT NULL AUTO_INCREMENT,
  `id_anggota`         INT(11)     NOT NULL,
  `id_buku`            INT(11)     NOT NULL,
  `tanggal_pinjam`     DATE        NOT NULL,
  `tanggal_kembali`    DATE,
  `status_peminjaman`  VARCHAR(50) NOT NULL DEFAULT 'Dipinjam',
  PRIMARY KEY (`id_pinjam`),
  CONSTRAINT `fk_pinjam_anggota`
    FOREIGN KEY (`id_anggota`) REFERENCES `anggota`(`id_anggota`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_pinjam_buku`
    FOREIGN KEY (`id_buku`) REFERENCES `buku`(`id_buku`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
-- 5. DATA FILE DIGITAL (E-BOOK FILE)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `file_digital` (
  `id_file`     INT(11)      NOT NULL AUTO_INCREMENT,
  `id_buku`     INT(11)      NOT NULL,
  `format_file` VARCHAR(10)  NOT NULL,
  `ukuran_file` VARCHAR(20),
  `link_unduh`  VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_file`),
  CONSTRAINT `fk_file_buku`
    FOREIGN KEY (`id_buku`) REFERENCES `buku`(`id_buku`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
-- SAMPLE DATA
-- ─────────────────────────────────────────────
INSERT INTO `kategori` (`nama_kategori`, `deskripsi`) VALUES
  ('Teknologi', 'Buku-buku tentang teknologi dan pemrograman'),
  ('Sastra',    'Karya sastra Indonesia dan mancanegara'),
  ('Sains',     'Buku ilmu pengetahuan alam dan eksakta');

INSERT INTO `anggota` (`nama_lengkap`, `email`, `no_telepon`, `tanggal_daftar`) VALUES
  ('Burhan Yusuf Arifin', 'burhan@example.com', '081234567890', '2026-01-10'),
  ('Arjuna Dwi Refa S',   'arjuna@example.com', '082345678901', '2026-01-15');

INSERT INTO `buku` (`id_kategori`, `judul_buku`, `penulis`, `penerbit`, `tahun_terbit`, `stok`) VALUES
  (1, 'Pemrograman Mobile Dasar', 'Ahmad Setiawan', 'Penerbit IT', 2023, 5),
  (2, 'Laskar Pelangi',           'Andrea Hirata',  'Bentang',    2005, 3),
  (1, 'Belajar REST API',         'Dian Pratama',   'TechPress',  2024, 7);

INSERT INTO `file_digital` (`id_buku`, `format_file`, `ukuran_file`, `link_unduh`) VALUES
  (1, 'PDF',  '15 MB', 'https://pemrogmobile.infinityfree.me/files/buku1.pdf'),
  (3, 'EPUB', '8 MB',  'https://pemrogmobile.infinityfree.me/files/buku3.epub');

INSERT INTO `peminjaman` (`id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali`, `status_peminjaman`) VALUES
  (1, 1, '2026-06-01', NULL,         'Dipinjam'),
  (2, 2, '2026-05-20', '2026-05-27', 'Kembali');
