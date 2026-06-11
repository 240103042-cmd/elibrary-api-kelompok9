<?php
// ============================================================
// peminjaman_create.php — Tambah peminjaman baru
// POST /api/peminjaman_create.php
// Body JSON: { id_anggota, id_buku, tanggal_pinjam, tanggal_kembali?, status_peminjaman? }
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(405, 'error', 'Hanya method POST yang diizinkan.');
}

$conn = getConnection();
$body = getRequestBody();

$required = ['id_anggota', 'id_buku', 'tanggal_pinjam'];
foreach ($required as $field) {
    if (empty($body[$field])) {
        response(400, 'error', "Field '$field' wajib diisi.");
    }
}

$id_anggota       = (int)$body['id_anggota'];
$id_buku          = (int)$body['id_buku'];
$tanggal_pinjam   = $body['tanggal_pinjam'];
$tanggal_kembali  = $body['tanggal_kembali']   ?? null;
$status           = $body['status_peminjaman'] ?? 'Dipinjam';

// Validasi anggota ada
$chkA = $conn->prepare("SELECT id_anggota FROM anggota WHERE id_anggota = ?");
$chkA->bind_param('i', $id_anggota);
$chkA->execute();
if ($chkA->get_result()->num_rows === 0) {
    response(404, 'error', "Anggota dengan ID $id_anggota tidak ditemukan.");
}

// Validasi buku ada & stok > 0
$chkB = $conn->prepare("SELECT stok FROM buku WHERE id_buku = ?");
$chkB->bind_param('i', $id_buku);
$chkB->execute();
$buku = $chkB->get_result()->fetch_assoc();
if (!$buku) {
    response(404, 'error', "Buku dengan ID $id_buku tidak ditemukan.");
}
if ((int)$buku['stok'] <= 0) {
    response(400, 'error', "Stok buku tidak tersedia.");
}

$stmt = $conn->prepare(
    "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status_peminjaman)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param('iisss', $id_anggota, $id_buku, $tanggal_pinjam, $tanggal_kembali, $status);

if ($stmt->execute()) {
    // Kurangi stok buku
    $conn->query("UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku");
    response(201, 'success', 'Peminjaman berhasil ditambahkan.', ['id_pinjam' => $conn->insert_id]);
}
response(500, 'error', 'Gagal menambahkan peminjaman.');
