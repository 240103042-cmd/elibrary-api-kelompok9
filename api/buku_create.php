<?php
// ============================================================
// buku_create.php — Tambah buku baru
// POST /api/buku_create.php
// Body JSON: { id_kategori, judul_buku, penulis, penerbit?, tahun_terbit?, stok? }
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(405, 'error', 'Hanya method POST yang diizinkan.');
}

$conn = getConnection();
$body = getRequestBody();

$required = ['id_kategori', 'judul_buku', 'penulis'];
foreach ($required as $field) {
    if (empty($body[$field])) {
        response(400, 'error', "Field '$field' wajib diisi.");
    }
}

$id_kategori  = (int)$body['id_kategori'];
$judul_buku   = $body['judul_buku'];
$penulis      = $body['penulis'];
$penerbit     = $body['penerbit']     ?? null;
$tahun_terbit = isset($body['tahun_terbit']) ? (int)$body['tahun_terbit'] : null;
$stok         = isset($body['stok'])         ? (int)$body['stok']         : 0;

// Validasi id_kategori ada
$kat = $conn->prepare("SELECT id_kategori FROM kategori WHERE id_kategori = ?");
$kat->bind_param('i', $id_kategori);
$kat->execute();
if ($kat->get_result()->num_rows === 0) {
    response(404, 'error', "Kategori dengan ID $id_kategori tidak ditemukan.");
}

$stmt = $conn->prepare(
    "INSERT INTO buku (id_kategori, judul_buku, penulis, penerbit, tahun_terbit, stok)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('isssii', $id_kategori, $judul_buku, $penulis, $penerbit, $tahun_terbit, $stok);

if ($stmt->execute()) {
    response(201, 'success', 'Buku berhasil ditambahkan.', ['id_buku' => $conn->insert_id]);
}
response(500, 'error', 'Gagal menambahkan buku.');
