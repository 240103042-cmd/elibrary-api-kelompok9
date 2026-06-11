<?php
// ============================================================
// kategori_view.php — Ambil data kategori
// GET /api/kategori_view.php         → semua kategori
// GET /api/kategori_view.php?id=1    → kategori by ID
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
    $stmt = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) response(404, 'error', "Kategori dengan ID $id tidak ditemukan.");
    response(200, 'success', 'Data kategori ditemukan.', $data);
} else {
    $result = $conn->query("SELECT * FROM kategori ORDER BY id_kategori ASC");
    $rows   = $result->fetch_all(MYSQLI_ASSOC);
    response(200, 'success', 'Berhasil mengambil seluruh data kategori.', $rows);
}
