<?php
// ============================================================
// anggota_create.php — Tambah anggota baru
// POST /api/anggota_create.php
// Body JSON: { nama_lengkap, email, no_telepon?, tanggal_daftar }
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(405, 'error', 'Hanya method POST yang diizinkan.');
}

$conn = getConnection();
$body = getRequestBody();

$required = ['nama_lengkap', 'email', 'tanggal_daftar'];
foreach ($required as $field) {
    if (empty($body[$field])) {
        response(400, 'error', "Field '$field' wajib diisi.");
    }
}

$nama           = $body['nama_lengkap'];
$email          = $body['email'];
$no_telepon     = $body['no_telepon']    ?? null;
$tanggal_daftar = $body['tanggal_daftar'];

// Cek email duplikat
$check = $conn->prepare("SELECT id_anggota FROM anggota WHERE email = ?");
$check->bind_param('s', $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    response(409, 'error', 'Email sudah terdaftar.');
}

$stmt = $conn->prepare(
    "INSERT INTO anggota (nama_lengkap, email, no_telepon, tanggal_daftar)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param('ssss', $nama, $email, $no_telepon, $tanggal_daftar);

if ($stmt->execute()) {
    response(201, 'success', 'Anggota berhasil ditambahkan.', ['id_anggota' => $conn->insert_id]);
}
response(500, 'error', 'Gagal menambahkan anggota.');
