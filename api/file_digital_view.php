<?php
// ============================================================
// file_digital_view.php — Ambil data file digital
// GET /api/file_digital_view.php         → semua file
// GET /api/file_digital_view.php?id=1    → file by ID
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
        "SELECT f.*, b.judul_buku
         FROM file_digital f
         JOIN buku b ON f.id_buku = b.id_buku
         WHERE f.id_file = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) response(404, 'error', "File digital dengan ID $id tidak ditemukan.");
    response(200, 'success', 'Data file digital ditemukan.', $data);
} else {
    $result = $conn->query(
        "SELECT f.*, b.judul_buku
         FROM file_digital f
         JOIN buku b ON f.id_buku = b.id_buku
         ORDER BY f.id_file ASC"
    );
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    response(200, 'success', 'Berhasil mengambil seluruh data file digital.', $rows);
}
