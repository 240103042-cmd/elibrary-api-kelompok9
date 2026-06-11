<?php
// ============================================================
// peminjaman_view.php — Ambil data peminjaman
// GET /api/peminjaman_view.php         → semua peminjaman
// GET /api/peminjaman_view.php?id=1    → peminjaman by ID
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
        "SELECT p.*, a.nama_lengkap, b.judul_buku
         FROM peminjaman p
         JOIN anggota a ON p.id_anggota = a.id_anggota
         JOIN buku b    ON p.id_buku    = b.id_buku
         WHERE p.id_pinjam = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) response(404, 'error', "Peminjaman dengan ID $id tidak ditemukan.");
    response(200, 'success', 'Data peminjaman ditemukan.', $data);
} else {
    $result = $conn->query(
        "SELECT p.*, a.nama_lengkap, b.judul_buku
         FROM peminjaman p
         JOIN anggota a ON p.id_anggota = a.id_anggota
         JOIN buku b    ON p.id_buku    = b.id_buku
         ORDER BY p.id_pinjam ASC"
    );
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    response(200, 'success', 'Berhasil mengambil seluruh data peminjaman.', $rows);
}
