<?php
// ============================================================
// buku_view.php — Ambil data buku
// GET /api/buku_view.php         → semua buku
// GET /api/buku_view.php?id=1    → buku by ID
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response(405, 'error', 'Hanya method GET yang diizinkan.');
}

$conn = getConnection();
$id   = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    $stmt = $conn->prepare(
        "SELECT b.*, k.nama_kategori
         FROM buku b
         JOIN kategori k ON b.id_kategori = k.id_kategori
         WHERE b.id_buku = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) response(404, 'error', "Buku dengan ID $id tidak ditemukan.");
    response(200, 'success', 'Data buku ditemukan.', $data);
} else {
    $result = $conn->query(
        "SELECT b.*, k.nama_kategori
         FROM buku b
         JOIN kategori k ON b.id_kategori = k.id_kategori
         ORDER BY b.id_buku ASC"
    );
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    response(200, 'success', 'Berhasil mengambil seluruh data buku.', $rows);
}
