<?php
// ============================================================
// file_digital_create.php — Tambah file digital baru
// POST /api/file_digital_create.php
// Body JSON: { id_buku, format_file, ukuran_file?, link_unduh }
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(405, 'error', 'Hanya method POST yang diizinkan.');
}

$conn = getConnection();
$body = getRequestBody();

$required = ['id_buku', 'format_file', 'link_unduh'];
foreach ($required as $field) {
    if (empty($body[$field])) {
        response(400, 'error', "Field '$field' wajib diisi.");
    }
}

$id_buku     = (int)$body['id_buku'];
$format_file = $body['format_file'];
$ukuran_file = $body['ukuran_file'] ?? null;
$link_unduh  = $body['link_unduh'];

// Validasi buku ada
$chkB = $conn->prepare("SELECT id_buku FROM buku WHERE id_buku = ?");
$chkB->bind_param('i', $id_buku);
$chkB->execute();
if ($chkB->get_result()->num_rows === 0) {
    response(404, 'error', "Buku dengan ID $id_buku tidak ditemukan.");
}

$stmt = $conn->prepare(
    "INSERT INTO file_digital (id_buku, format_file, ukuran_file, link_unduh)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param('isss', $id_buku, $format_file, $ukuran_file, $link_unduh);

if ($stmt->execute()) {
    response(201, 'success', 'File digital berhasil ditambahkan.', ['id_file' => $conn->insert_id]);
}
response(500, 'error', 'Gagal menambahkan file digital.');
