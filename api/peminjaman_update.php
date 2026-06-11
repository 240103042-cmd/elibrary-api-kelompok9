<?php
// ============================================================
// peminjaman_update.php — Update data peminjaman
// PUT /api/peminjaman_update.php?id=1
// Body JSON: { tanggal_kembali?, status_peminjaman? }
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

$check = $conn->prepare("SELECT * FROM peminjaman WHERE id_pinjam = ?");
$check->bind_param('i', $id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
if (!$existing) response(404, 'error', "Peminjaman dengan ID $id tidak ditemukan.");

$id_anggota      = isset($body['id_anggota'])        ? (int)$body['id_anggota']       : (int)$existing['id_anggota'];
$id_buku         = isset($body['id_buku'])            ? (int)$body['id_buku']          : (int)$existing['id_buku'];
$tanggal_pinjam  = $body['tanggal_pinjam']            ?? $existing['tanggal_pinjam'];
$tanggal_kembali = $body['tanggal_kembali']           ?? $existing['tanggal_kembali'];
$status          = $body['status_peminjaman']         ?? $existing['status_peminjaman'];

$stmt = $conn->prepare(
    "UPDATE peminjaman SET id_anggota=?, id_buku=?, tanggal_pinjam=?,
     tanggal_kembali=?, status_peminjaman=? WHERE id_pinjam=?"
);
$stmt->bind_param('iisssi', $id_anggota, $id_buku, $tanggal_pinjam, $tanggal_kembali, $status, $id);

if ($stmt->execute()) {
    response(200, 'success', "Peminjaman ID $id berhasil diperbarui.");
}
response(500, 'error', 'Gagal memperbarui peminjaman.');
