<?php
// ============================================================
// buku_update.php — Update data buku
// PUT /api/buku_update.php?id=1
// Body JSON: { id_kategori?, judul_buku?, penulis?, penerbit?, tahun_terbit?, stok? }
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    response(405, 'error', 'Hanya method PUT yang diizinkan.');
}

$conn = getConnection();
$id   = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) response(400, 'error', 'Parameter id diperlukan.');

$body = getRequestBody();

$check = $conn->prepare("SELECT * FROM buku WHERE id_buku = ?");
$check->bind_param('i', $id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
if (!$existing) response(404, 'error', "Buku dengan ID $id tidak ditemukan.");

$id_kategori  = isset($body['id_kategori'])  ? (int)$body['id_kategori']  : (int)$existing['id_kategori'];
$judul_buku   = $body['judul_buku']   ?? $existing['judul_buku'];
$penulis      = $body['penulis']      ?? $existing['penulis'];
$penerbit     = $body['penerbit']     ?? $existing['penerbit'];
$tahun_terbit = isset($body['tahun_terbit']) ? (int)$body['tahun_terbit'] : (int)$existing['tahun_terbit'];
$stok         = isset($body['stok'])         ? (int)$body['stok']         : (int)$existing['stok'];

$stmt = $conn->prepare(
    "UPDATE buku SET id_kategori=?, judul_buku=?, penulis=?, penerbit=?,
     tahun_terbit=?, stok=? WHERE id_buku=?"
);
$stmt->bind_param('isssiii', $id_kategori, $judul_buku, $penulis, $penerbit, $tahun_terbit, $stok, $id);

if ($stmt->execute()) {
    response(200, 'success', "Buku ID $id berhasil diperbarui.");
}
response(500, 'error', 'Gagal memperbarui buku.');
