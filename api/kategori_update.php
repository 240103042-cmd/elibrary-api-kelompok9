<?php
// ============================================================
// kategori_update.php — Update data kategori
// PUT /api/kategori_update.php?id=1
// Body JSON: { nama_kategori?, deskripsi? }
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

$check = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
$check->bind_param('i', $id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
if (!$existing) response(404, 'error', "Kategori dengan ID $id tidak ditemukan.");

$nama_kategori = $body['nama_kategori'] ?? $existing['nama_kategori'];
$deskripsi     = $body['deskripsi']     ?? $existing['deskripsi'];

$stmt = $conn->prepare(
    "UPDATE kategori SET nama_kategori=?, deskripsi=? WHERE id_kategori=?"
);
$stmt->bind_param('ssi', $nama_kategori, $deskripsi, $id);

if ($stmt->execute()) {
    response(200, 'success', "Kategori ID $id berhasil diperbarui.");
}
response(500, 'error', 'Gagal memperbarui kategori.');
