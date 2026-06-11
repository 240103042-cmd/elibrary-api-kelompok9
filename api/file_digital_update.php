<?php
// ============================================================
// file_digital_update.php — Update data file digital
// PUT /api/file_digital_update.php?id=1
// Body JSON: { id_buku?, format_file?, ukuran_file?, link_unduh? }
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

$check = $conn->prepare("SELECT * FROM file_digital WHERE id_file = ?");
$check->bind_param('i', $id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
if (!$existing) response(404, 'error', "File digital dengan ID $id tidak ditemukan.");

$id_buku     = isset($body['id_buku']) ? (int)$body['id_buku'] : (int)$existing['id_buku'];
$format_file = $body['format_file'] ?? $existing['format_file'];
$ukuran_file = $body['ukuran_file'] ?? $existing['ukuran_file'];
$link_unduh  = $body['link_unduh']  ?? $existing['link_unduh'];

$stmt = $conn->prepare(
    "UPDATE file_digital SET id_buku=?, format_file=?, ukuran_file=?, link_unduh=?
     WHERE id_file=?"
);
$stmt->bind_param('isssi', $id_buku, $format_file, $ukuran_file, $link_unduh, $id);

if ($stmt->execute()) {
    response(200, 'success', "File digital ID $id berhasil diperbarui.");
}
response(500, 'error', 'Gagal memperbarui file digital.');
