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
-- DATA LENGKAP — 10 Kategori, 20 Anggota,
-- 60 Buku, 55 File Digital, 35 Peminjaman
-- Sumber buku: Project Gutenberg & Archive.org
-- ─────────────────────────────────────────────

-- ── KATEGORI ─────────────────────────────────
INSERT INTO `kategori` (`nama_kategori`, `deskripsi`) VALUES
  ('Sastra Indonesia',       'Karya sastra klasik dan modern dari penulis Indonesia'),
  ('Sastra Internasional',   'Karya sastra klasik dunia yang telah masuk domain publik'),
  ('Teknologi & Pemrograman','Buku-buku ilmu komputer, pemrograman, dan teknologi informasi'),
  ('Sains & Matematika',     'Buku ilmu pengetahuan alam, fisika, biologi, dan matematika'),
  ('Sejarah & Budaya',       'Buku sejarah Indonesia dan dunia serta kajian budaya'),
  ('Ekonomi & Bisnis',       'Buku ekonomi, manajemen, kewirausahaan, dan keuangan'),
  ('Filsafat',               'Karya filsafat dari era kuno hingga modern'),
  ('Kesehatan & Kedokteran', 'Buku ilmu kesehatan, kedokteran, dan psikologi'),
  ('Hukum & Politik',        'Buku hukum, tata negara, dan ilmu politik'),
  ('Agama & Spiritualitas',  'Buku kajian agama, spiritualitas, dan pengembangan diri');

-- ── ANGGOTA (20 orang) ───────────────────────
INSERT INTO `anggota` (`nama_lengkap`, `email`, `no_telepon`, `tanggal_daftar`) VALUES
  ('Burhan Yusuf Arifin',    'burhan@example.com',     '081234567890', '2026-01-10'),
  ('Arjuna Dwi Refa S',      'arjuna@example.com',     '082345678901', '2026-01-15'),
  ('Siti Rahayu Wulandari',  'siti.rahayu@example.com','083456789012', '2026-01-20'),
  ('Muhammad Fajar Nugroho', 'fajar.nugroho@example.com','084567890123','2026-01-22'),
  ('Dewi Kartika Sari',      'dewi.kartika@example.com','085678901234','2026-01-25'),
  ('Rizky Maulana Pratama',  'rizky.m@example.com',    '086789012345', '2026-02-01'),
  ('Ayu Lestari Ningrum',    'ayu.lestari@example.com','087890123456', '2026-02-05'),
  ('Dimas Eka Saputra',      'dimas.eka@example.com',  '088901234567', '2026-02-10'),
  ('Fitri Handayani',        'fitri.h@example.com',    '089012345678', '2026-02-14'),
  ('Hendra Wijaya',          'hendra.w@example.com',   '081123456789', '2026-02-18'),
  ('Indah Permata Sari',     'indah.p@example.com',    '082234567890', '2026-02-20'),
  ('Joko Susanto',           'joko.susanto@example.com','083345678901','2026-02-25'),
  ('Kartini Wahyuningsih',   'kartini.w@example.com',  '084456789012', '2026-03-01'),
  ('Lukman Hakim',           'lukman.h@example.com',   '085567890123', '2026-03-05'),
  ('Maya Anggraini',         'maya.a@example.com',     '086678901234', '2026-03-10'),
  ('Naufal Ardiansyah',      'naufal.a@example.com',   '087789012345', '2026-03-12'),
  ('Olivia Rahmawati',       'olivia.r@example.com',   '088890123456', '2026-03-15'),
  ('Putra Ramadhan',         'putra.r@example.com',    '089901234567', '2026-03-18'),
  ('Qonita Hasanah',         'qonita.h@example.com',   '081012345678', '2026-03-20'),
  ('Reza Firmansyah',        'reza.f@example.com',     '082123456789', '2026-03-25');

-- ── BUKU (60 judul) ──────────────────────────
-- Sastra Indonesia (id_kategori = 1)
INSERT INTO `buku` (`id_kategori`, `judul_buku`, `penulis`, `penerbit`, `tahun_terbit`, `stok`) VALUES
  (1, 'Siti Nurbaya: Kasih Tak Sampai',        'Marah Rusli',               'Balai Pustaka',    1922, 5),
  (1, 'Salah Asuhan',                           'Abdul Muis',                'Balai Pustaka',    1928, 4),
  (1, 'Atheis',                                 'Achdiat K. Mihardja',       'Balai Pustaka',    1949, 3),
  (1, 'Layar Terkembang',                       'Sutan Takdir Alisjahbana',  'Balai Pustaka',    1936, 4),
  (1, 'Belenggu',                               'Armijn Pane',               'Pustaka Rakyat',   1940, 3),
  (1, 'Bumi Manusia',                           'Pramoedya Ananta Toer',     'Hasta Mitra',      1980, 6),
  (1, 'Anak Semua Bangsa',                      'Pramoedya Ananta Toer',     'Hasta Mitra',      1980, 5),
  (1, 'Jejak Langkah',                          'Pramoedya Ananta Toer',     'Hasta Mitra',      1985, 4),
  (1, 'Rumah Kaca',                             'Pramoedya Ananta Toer',     'Hasta Mitra',      1988, 4),
  (1, 'Tenggelamnya Kapal Van der Wijck',       'Buya Hamka',                'Balai Pustaka',    1938, 5),
  (1, 'Di Bawah Lindungan Ka''bah',             'Buya Hamka',                'Balai Pustaka',    1938, 5),
  (1, 'Robohnya Surau Kami',                    'A.A. Navis',                'NV Nusantara',     1956, 3),
  (1, 'Laskar Pelangi',                         'Andrea Hirata',             'Bentang',          2005, 7),
  (1, 'Sang Pemimpi',                           'Andrea Hirata',             'Bentang',          2006, 6),
  (1, 'Edensor',                                'Andrea Hirata',             'Bentang',          2007, 5),
  (1, 'Maryamah Karpov',                        'Andrea Hirata',             'Bentang',          2008, 4),
  (1, 'Negeri 5 Menara',                        'Ahmad Fuadi',               'Gramedia',         2009, 6),
  (1, 'Ranah 3 Warna',                          'Ahmad Fuadi',               'Gramedia',         2011, 5),
  (1, 'Rantau 1 Muara',                         'Ahmad Fuadi',               'Gramedia',         2013, 4),
  (1, 'Max Havelaar',                           'Multatuli',                 'Just van Dorp',    1860, 3),

-- Sastra Internasional (id_kategori = 2)
  (2, 'Pride and Prejudice',                    'Jane Austen',               'T. Egerton',       1813, 8),
  (2, 'Sense and Sensibility',                  'Jane Austen',               'T. Egerton',       1811, 6),
  (2, 'Emma',                                   'Jane Austen',               'John Murray',      1815, 5),
  (2, 'Northanger Abbey',                       'Jane Austen',               'John Murray',      1817, 4),
  (2, 'Oliver Twist',                           'Charles Dickens',           'Richard Bentley',  1838, 6),
  (2, 'Great Expectations',                     'Charles Dickens',           'Chapman & Hall',   1861, 7),
  (2, 'A Tale of Two Cities',                   'Charles Dickens',           'Chapman & Hall',   1859, 6),
  (2, 'Adventures of Huckleberry Finn',         'Mark Twain',                'Chatto & Windus',  1884, 7),
  (2, 'The Adventures of Tom Sawyer',           'Mark Twain',                'American Publishing',1876,6),
  (2, 'Moby Dick',                              'Herman Melville',           'Harper & Brothers',1851, 5),
  (2, 'Crime and Punishment',                   'Fyodor Dostoevsky',         'The Russian Messenger',1866,5),
  (2, 'The Brothers Karamazov',                 'Fyodor Dostoevsky',         'The Russian Messenger',1880,4),
  (2, 'War and Peace',                          'Leo Tolstoy',               'The Russian Messenger',1869,5),
  (2, 'Anna Karenina',                          'Leo Tolstoy',               'The Russian Messenger',1878,6),
  (2, 'Les Misérables',                         'Victor Hugo',               'A. Lacroix',       1862, 5),
  (2, 'The Count of Monte Cristo',              'Alexandre Dumas',           'Journal des Débats',1844,6),
  (2, 'Frankenstein',                           'Mary Shelley',              'Lackington Hughes',1818, 5),
  (2, 'Dracula',                                'Bram Stoker',               'Archibald Constable',1897,6),
  (2, 'The Picture of Dorian Gray',             'Oscar Wilde',               'Ward Lock & Co',   1890, 5),
  (2, 'A Study in Scarlet',                     'Arthur Conan Doyle',        'Ward Lock & Co',   1887, 6),
  (2, 'Around the World in 80 Days',            'Jules Verne',               'Pierre-Jules Hetzel',1872,6),
  (2, 'Twenty Thousand Leagues Under the Sea',  'Jules Verne',               'Pierre-Jules Hetzel',1870,5),
  (2, 'Alice''s Adventures in Wonderland',      'Lewis Carroll',             'Macmillan',        1865, 7),
  (2, 'Treasure Island',                        'Robert Louis Stevenson',    'Cassell & Co',     1883, 6),
  (2, 'The Time Machine',                       'H.G. Wells',                'William Heinemann',1895, 5),
  (2, 'The War of the Worlds',                  'H.G. Wells',                'William Heinemann',1898, 5),

-- Teknologi & Pemrograman (id_kategori = 3)
  (3, 'Structure and Interpretation of Computer Programs', 'Harold Abelson & Gerald Sussman', 'MIT Press', 1996, 4),
  (3, 'Introduction to Algorithms',             'Cormen, Leiserson, Rivest, Stein', 'MIT Press', 2009, 5),
  (3, 'Clean Code',                             'Robert C. Martin',          'Prentice Hall',    2008, 6),
  (3, 'The Pragmatic Programmer',               'Andrew Hunt & David Thomas', 'Addison-Wesley', 1999, 5),
  (3, 'Design Patterns',                        'Gang of Four',              'Addison-Wesley',   1994, 4),
  (3, 'Database Design for Mere Mortals',       'Michael J. Hernandez',      'Addison-Wesley',   2013, 4),
  (3, 'Pemrograman Mobile Dasar',               'Ahmad Setiawan',            'Penerbit IT',      2023, 7),
  (3, 'Belajar REST API dengan PHP',            'Dian Pratama',              'TechPress',        2024, 8),

-- Sains & Matematika (id_kategori = 4)
  (4, 'A Brief History of Time',                'Stephen Hawking',           'Bantam Books',     1988, 6),
  (4, 'The Origin of Species',                  'Charles Darwin',            'John Murray',      1859, 5),
  (4, 'Cosmos',                                 'Carl Sagan',                'Random House',     1980, 5),
  (4, 'Surely You''re Joking, Mr. Feynman!',    'Richard P. Feynman',        'W. W. Norton',     1985, 5),
  (4, 'Thinking, Fast and Slow',                'Daniel Kahneman',           'Farrar Straus',    2011, 6),

-- Sejarah & Budaya (id_kategori = 5)
  (5, 'Sapiens: A Brief History of Humankind',  'Yuval Noah Harari',         'Harper Collins',   2011, 6),
  (5, 'Guns, Germs, and Steel',                 'Jared Diamond',             'W. W. Norton',     1997, 5),
  (5, 'Sejarah Indonesia Modern 1200–2008',     'M.C. Ricklefs',             'Gadjah Mada UP',   2008, 4),

-- Ekonomi & Bisnis (id_kategori = 6)
  (6, 'The Wealth of Nations',                  'Adam Smith',                'Strahan & Cadell', 1776, 5),
  (6, 'Rich Dad Poor Dad',                      'Robert T. Kiyosaki',        'Warner Books',     2000, 7),
  (6, 'Freakonomics',                           'Steven D. Levitt & Stephen J. Dubner', 'William Morrow', 2005, 6),
  (6, 'Zero to One',                            'Peter Thiel',               'Crown Business',   2014, 6),
  (6, 'The Lean Startup',                       'Eric Ries',                 'Crown Business',   2011, 5),

-- Filsafat (id_kategori = 7)
  (7, 'Meditations',                            'Marcus Aurelius',           'Public Domain',    180,  4),
  (7, 'The Republic',                           'Plato',                     'Public Domain',    380,  4),
  (7, 'Nicomachean Ethics',                     'Aristotle',                 'Public Domain',    340,  3),

-- Kesehatan & Kedokteran (id_kategori = 8)
  (8, 'The Body Keeps the Score',               'Bessel van der Kolk',       'Viking',           2014, 5),
  (8, 'Grain Brain',                            'David Perlmutter',          'Little Brown',     2013, 4),

-- Hukum & Politik (id_kategori = 9)
  (9, 'Pengantar Hukum Indonesia',              'Sudikno Mertokusumo',       'Liberty',          2010, 4),
  (9, 'Hukum Tata Negara Indonesia',            'Jimly Asshiddiqie',         'Konstitusi Press', 2006, 3),

-- Agama & Spiritualitas (id_kategori = 10)
  (10,'Tafsir Al-Misbah Vol. 1',               'M. Quraish Shihab',         'Lentera Hati',     2002, 5),
  (10,'Ihya Ulumiddin Vol. 1',                 'Imam Al-Ghazali',           'Dar al-Minhaj',    1107, 3);

-- ── FILE DIGITAL (55 entri, link Project Gutenberg & Archive.org) ──
INSERT INTO `file_digital` (`id_buku`, `format_file`, `ukuran_file`, `link_unduh`) VALUES
-- Sastra Indonesia
  (1,  'PDF',  '3.2 MB',  'https://archive.org/download/sitinurbaya/siti_nurbaya.pdf'),
  (1,  'EPUB', '1.1 MB',  'https://archive.org/download/sitinurbaya/siti_nurbaya.epub'),
  (2,  'PDF',  '2.8 MB',  'https://archive.org/download/salahasuhan/salah_asuhan.pdf'),
  (3,  'PDF',  '2.5 MB',  'https://archive.org/download/atheis_achdiat/atheis.pdf'),
  (4,  'PDF',  '2.1 MB',  'https://archive.org/download/layarterkembang/layar_terkembang.pdf'),
  (5,  'PDF',  '1.9 MB',  'https://archive.org/download/belenggu_armijn/belenggu.pdf'),
  (6,  'PDF',  '4.5 MB',  'https://archive.org/download/bumimanusia/bumi_manusia.pdf'),
  (6,  'EPUB', '1.8 MB',  'https://archive.org/download/bumimanusia/bumi_manusia.epub'),
  (7,  'PDF',  '4.3 MB',  'https://archive.org/download/anaksemuabangsa/anak_semua_bangsa.pdf'),
  (8,  'PDF',  '4.1 MB',  'https://archive.org/download/jejaklangkah/jejak_langkah.pdf'),
  (9,  'PDF',  '3.9 MB',  'https://archive.org/download/rumahkaca/rumah_kaca.pdf'),
  (10, 'PDF',  '3.4 MB',  'https://archive.org/download/tenggelamnyakapal/tenggelamnya_kapal.pdf'),
  (10, 'EPUB', '1.2 MB',  'https://archive.org/download/tenggelamnyakapal/tenggelamnya_kapal.epub'),
  (11, 'PDF',  '2.9 MB',  'https://archive.org/download/dibawahlindingan/di_bawah_lindungan.pdf'),
  (12, 'PDF',  '1.6 MB',  'https://archive.org/download/robohnyasuraukami/robohnya_surau_kami.pdf'),
  (13, 'PDF',  '5.2 MB',  'https://archive.org/download/laskarpelangi/laskar_pelangi.pdf'),
  (13, 'EPUB', '2.1 MB',  'https://archive.org/download/laskarpelangi/laskar_pelangi.epub'),
  (17, 'PDF',  '4.8 MB',  'https://archive.org/download/negeri5menara/negeri_5_menara.pdf'),
  (20, 'PDF',  '6.1 MB',  'https://archive.org/download/maxhavelaar_mul/max_havelaar.pdf'),
  (20, 'EPUB', '2.4 MB',  'https://archive.org/download/maxhavelaar_mul/max_havelaar.epub'),

-- Sastra Internasional (Project Gutenberg)
  (21, 'PDF',  '1.5 MB',  'https://www.gutenberg.org/files/1342/1342-pdf.pdf'),
  (21, 'EPUB', '0.4 MB',  'https://www.gutenberg.org/ebooks/1342.epub.images'),
  (22, 'PDF',  '1.3 MB',  'https://www.gutenberg.org/files/161/161-pdf.pdf'),
  (22, 'EPUB', '0.4 MB',  'https://www.gutenberg.org/ebooks/161.epub.images'),
  (23, 'PDF',  '1.6 MB',  'https://www.gutenberg.org/files/158/158-pdf.pdf'),
  (23, 'EPUB', '0.5 MB',  'https://www.gutenberg.org/ebooks/158.epub.images'),
  (24, 'PDF',  '1.1 MB',  'https://www.gutenberg.org/files/121/121-pdf.pdf'),
  (25, 'PDF',  '1.8 MB',  'https://www.gutenberg.org/files/730/730-pdf.pdf'),
  (25, 'EPUB', '0.6 MB',  'https://www.gutenberg.org/ebooks/730.epub.images'),
  (26, 'PDF',  '2.1 MB',  'https://www.gutenberg.org/files/1400/1400-pdf.pdf'),
  (26, 'EPUB', '0.7 MB',  'https://www.gutenberg.org/ebooks/1400.epub.images'),
  (27, 'PDF',  '1.9 MB',  'https://www.gutenberg.org/files/98/98-pdf.pdf'),
  (27, 'EPUB', '0.6 MB',  'https://www.gutenberg.org/ebooks/98.epub.images'),
  (28, 'PDF',  '1.4 MB',  'https://www.gutenberg.org/files/76/76-pdf.pdf'),
  (28, 'EPUB', '0.5 MB',  'https://www.gutenberg.org/ebooks/76.epub.images'),
  (29, 'PDF',  '1.2 MB',  'https://www.gutenberg.org/files/74/74-pdf.pdf'),
  (30, 'PDF',  '3.8 MB',  'https://www.gutenberg.org/files/2701/2701-pdf.pdf'),
  (30, 'EPUB', '1.2 MB',  'https://www.gutenberg.org/ebooks/2701.epub.images'),
  (31, 'PDF',  '2.2 MB',  'https://www.gutenberg.org/files/2554/2554-pdf.pdf'),
  (31, 'EPUB', '0.9 MB',  'https://www.gutenberg.org/ebooks/2554.epub.images'),
  (33, 'PDF',  '8.5 MB',  'https://www.gutenberg.org/files/2600/2600-pdf.pdf'),
  (33, 'EPUB', '3.1 MB',  'https://www.gutenberg.org/ebooks/2600.epub.images'),
  (35, 'PDF',  '5.4 MB',  'https://www.gutenberg.org/files/135/135-pdf.pdf'),
  (35, 'EPUB', '1.9 MB',  'https://www.gutenberg.org/ebooks/135.epub.images'),
  (36, 'PDF',  '4.1 MB',  'https://www.gutenberg.org/files/1184/1184-pdf.pdf'),
  (36, 'EPUB', '1.5 MB',  'https://www.gutenberg.org/ebooks/1184.epub.images'),
  (37, 'PDF',  '0.8 MB',  'https://www.gutenberg.org/files/84/84-pdf.pdf'),
  (37, 'EPUB', '0.3 MB',  'https://www.gutenberg.org/ebooks/84.epub.images'),
  (38, 'PDF',  '1.0 MB',  'https://www.gutenberg.org/files/345/345-pdf.pdf'),
  (38, 'EPUB', '0.4 MB',  'https://www.gutenberg.org/ebooks/345.epub.images'),
  (39, 'PDF',  '0.9 MB',  'https://www.gutenberg.org/files/174/174-pdf.pdf'),
  (39, 'EPUB', '0.3 MB',  'https://www.gutenberg.org/ebooks/174.epub.images'),
  (40, 'PDF',  '0.7 MB',  'https://www.gutenberg.org/files/244/244-pdf.pdf'),
  (41, 'PDF',  '0.8 MB',  'https://www.gutenberg.org/files/103/103-pdf.pdf'),
  (41, 'EPUB', '0.3 MB',  'https://www.gutenberg.org/ebooks/103.epub.images'),
  (42, 'PDF',  '1.1 MB',  'https://www.gutenberg.org/files/164/164-pdf.pdf'),
  (43, 'PDF',  '0.6 MB',  'https://www.gutenberg.org/files/11/11-pdf.pdf'),
  (43, 'EPUB', '0.2 MB',  'https://www.gutenberg.org/ebooks/11.epub.images'),
  (44, 'PDF',  '1.0 MB',  'https://www.gutenberg.org/files/120/120-pdf.pdf'),
  (44, 'EPUB', '0.4 MB',  'https://www.gutenberg.org/ebooks/120.epub.images'),
  (45, 'PDF',  '0.7 MB',  'https://www.gutenberg.org/files/35/35-pdf.pdf'),
  (46, 'PDF',  '0.9 MB',  'https://www.gutenberg.org/files/36/36-pdf.pdf'),

-- Teknologi & Pemrograman
  (47, 'PDF',  '7.2 MB',  'https://mitpress.mit.edu/9780262510875/'),
  (48, 'PDF',  '9.8 MB',  'https://archive.org/download/IntroductionToAlgorithms/Introduction_to_Algorithms.pdf'),
  (49, 'PDF',  '3.5 MB',  'https://archive.org/download/CleanCode/clean_code.pdf'),
  (50, 'PDF',  '4.1 MB',  'https://archive.org/download/thepragmaticprogrammer/pragmatic_programmer.pdf'),
  (53, 'PDF',  '6.4 MB',  'https://archive.org/download/PemrogramanMobile/pemrograman_mobile_dasar.pdf'),
  (54, 'PDF',  '5.1 MB',  'https://archive.org/download/BelajarRESTAPI/belajar_rest_api_php.pdf'),

-- Sains & Matematika
  (55, 'PDF',  '2.8 MB',  'https://archive.org/download/ABriefHistoryofTime/brief_history_time.pdf'),
  (55, 'EPUB', '1.0 MB',  'https://archive.org/download/ABriefHistoryofTime/brief_history_time.epub'),
  (56, 'PDF',  '2.9 MB',  'https://www.gutenberg.org/files/1228/1228-pdf.pdf'),
  (56, 'EPUB', '1.1 MB',  'https://www.gutenberg.org/ebooks/1228.epub.images'),
  (57, 'PDF',  '3.5 MB',  'https://archive.org/download/cosmos_carlsagan/cosmos_carl_sagan.pdf'),
  (59, 'PDF',  '4.2 MB',  'https://archive.org/download/ThinkingFastandSlow/thinking_fast_slow.pdf'),

-- Sejarah & Budaya
  (60, 'PDF',  '6.1 MB',  'https://archive.org/download/SapiensHarari/sapiens_harari.pdf'),
  (61, 'PDF',  '5.7 MB',  'https://archive.org/download/GunsGermsSteel/guns_germs_steel.pdf'),

-- Ekonomi & Bisnis
  (63, 'PDF',  '4.4 MB',  'https://www.gutenberg.org/files/3300/3300-pdf.pdf'),
  (63, 'EPUB', '1.7 MB',  'https://www.gutenberg.org/ebooks/3300.epub.images'),
  (64, 'PDF',  '3.2 MB',  'https://archive.org/download/RichDadPoorDad/rich_dad_poor_dad.pdf'),
  (66, 'PDF',  '2.5 MB',  'https://archive.org/download/ZeroToOne_Thiel/zero_to_one.pdf'),
  (67, 'PDF',  '3.0 MB',  'https://archive.org/download/TheLeanStartup/lean_startup.pdf'),

-- Filsafat (Project Gutenberg)
  (68, 'PDF',  '0.9 MB',  'https://www.gutenberg.org/files/2680/2680-pdf.pdf'),
  (68, 'EPUB', '0.3 MB',  'https://www.gutenberg.org/ebooks/2680.epub.images'),
  (69, 'PDF',  '1.8 MB',  'https://www.gutenberg.org/files/1497/1497-pdf.pdf'),
  (69, 'EPUB', '0.7 MB',  'https://www.gutenberg.org/ebooks/1497.epub.images'),
  (70, 'PDF',  '1.5 MB',  'https://www.gutenberg.org/files/8438/8438-pdf.pdf');

-- ── PEMINJAMAN (35 catatan) ───────────────────
INSERT INTO `peminjaman` (`id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali`, `status_peminjaman`) VALUES
  (1,  6,  '2026-01-15', '2026-01-29', 'Kembali'),
  (1,  21, '2026-02-01', '2026-02-15', 'Kembali'),
  (1,  55, '2026-03-10', NULL,         'Dipinjam'),
  (2,  13, '2026-01-20', '2026-02-03', 'Kembali'),
  (2,  31, '2026-02-10', '2026-02-24', 'Kembali'),
  (2,  48, '2026-04-01', NULL,         'Dipinjam'),
  (3,  10, '2026-01-25', '2026-02-08', 'Kembali'),
  (3,  22, '2026-02-15', '2026-03-01', 'Kembali'),
  (3,  64, '2026-04-05', '2026-04-19', 'Kembali'),
  (4,  37, '2026-02-01', '2026-02-15', 'Kembali'),
  (4,  43, '2026-03-01', '2026-03-15', 'Kembali'),
  (4,  53, '2026-05-10', NULL,         'Dipinjam'),
  (5,  1,  '2026-02-05', '2026-02-19', 'Kembali'),
  (5,  26, '2026-03-05', '2026-03-19', 'Kembali'),
  (5,  60, '2026-05-01', '2026-05-15', 'Kembali'),
  (6,  35, '2026-02-10', '2026-02-24', 'Kembali'),
  (6,  47, '2026-03-15', '2026-03-29', 'Kembali'),
  (6,  68, '2026-05-20', NULL,         'Dipinjam'),
  (7,  4,  '2026-02-20', '2026-03-06', 'Kembali'),
  (7,  30, '2026-03-20', '2026-04-03', 'Kembali'),
  (7,  56, '2026-05-15', NULL,         'Dipinjam'),
  (8,  17, '2026-03-01', '2026-03-15', 'Kembali'),
  (8,  39, '2026-04-01', '2026-04-15', 'Kembali'),
  (8,  66, '2026-05-25', NULL,         'Dipinjam'),
  (9,  8,  '2026-03-10', '2026-03-24', 'Kembali'),
  (9,  25, '2026-04-10', '2026-04-24', 'Kembali'),
  (9,  54, '2026-06-01', NULL,         'Dipinjam'),
  (10, 36, '2026-03-15', '2026-03-29', 'Kembali'),
  (10, 44, '2026-04-15', '2026-04-29', 'Kembali'),
  (10, 69, '2026-06-05', NULL,         'Dipinjam'),
  (11, 2,  '2026-04-01', '2026-04-15', 'Kembali'),
  (12, 27, '2026-04-05', '2026-04-19', 'Kembali'),
  (13, 41, '2026-04-10', '2026-04-24', 'Kembali'),
  (14, 63, '2026-05-01', '2026-05-15', 'Kembali'),
  (15, 9,  '2026-05-05', NULL,         'Dipinjam');

-- ─────────────────────────────────────────────
-- 6. TABEL ADMIN
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin`     INT(11)      NOT NULL AUTO_INCREMENT,
  `username`     VARCHAR(50)  NOT NULL UNIQUE,
  `password`     VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `email`        VARCHAR(100) NOT NULL UNIQUE,
  `level`        ENUM('superadmin','admin','operator') NOT NULL DEFAULT 'admin',
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password default: Admin@1234 (di-hash dengan bcrypt)
INSERT INTO `admin` (`username`, `password`, `nama_lengkap`, `email`, `level`) VALUES
  ('superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 'superadmin@elibrary.com', 'superadmin'),
  ('admin1',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Burhan Yusuf Arifin',  'burhan.admin@elibrary.com', 'admin'),
  ('operator1',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Arjuna Dwi Refa S',    'arjuna.admin@elibrary.com', 'operator');

