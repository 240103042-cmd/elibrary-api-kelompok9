<?php
// ============================================================
// kategori_create.php — Tambah kategori baru
// POST /api/kategori_create.php
// Body JSON: { nama_kategori, deskripsi? }
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(405, 'error', 'Hanya method POST yang diizinkan.');
}

$conn = getConnection();
$body = getRequestBody();

if (empty($body['nama_kategori'])) {
    response(400, 'error', "Field 'nama_kategori' wajib diisi.");
}

$nama_kategori = $body['nama_kategori'];
$deskripsi     = $body['deskripsi'] ?? null;

// Cek duplikat nama
$check = $conn->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ?");
$check->bind_param('s', $nama_kategori);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    response(409, 'error', 'Nama kategori sudah ada.');
}

$stmt = $conn->prepare("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)");
$stmt->bind_param('ss', $nama_kategori, $deskripsi);

if ($stmt->execute()) {
    response(201, 'success', 'Kategori berhasil ditambahkan.', ['id_kategori' => $conn->insert_id]);
}
response(500, 'error', 'Gagal menambahkan kategori.');
