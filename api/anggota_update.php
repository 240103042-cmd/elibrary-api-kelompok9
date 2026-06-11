<?php
// ============================================================
// anggota_update.php — Update data anggota
// PUT /api/anggota_update.php?id=1
// Body JSON: { nama_lengkap?, email?, no_telepon?, tanggal_daftar? }
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

$check = $conn->prepare("SELECT * FROM anggota WHERE id_anggota = ?");
$check->bind_param('i', $id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
if (!$existing) response(404, 'error', "Anggota dengan ID $id tidak ditemukan.");

$nama           = $body['nama_lengkap']   ?? $existing['nama_lengkap'];
$email          = $body['email']           ?? $existing['email'];
$no_telepon     = $body['no_telepon']      ?? $existing['no_telepon'];
$tanggal_daftar = $body['tanggal_daftar']  ?? $existing['tanggal_daftar'];

$stmt = $conn->prepare(
    "UPDATE anggota SET nama_lengkap=?, email=?, no_telepon=?, tanggal_daftar=?
     WHERE id_anggota=?"
);
$stmt->bind_param('ssssi', $nama, $email, $no_telepon, $tanggal_daftar, $id);

if ($stmt->execute()) {
    response(200, 'success', "Anggota ID $id berhasil diperbarui.");
}
response(500, 'error', 'Gagal memperbarui anggota.');
