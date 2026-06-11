<?php
// ============================================================
// anggota_view.php — Ambil data anggota
// GET /api/anggota_view.php         → semua anggota
// GET /api/anggota_view.php?id=1    → anggota by ID
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
    $stmt = $conn->prepare("SELECT * FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) response(404, 'error', "Anggota dengan ID $id tidak ditemukan.");
    response(200, 'success', 'Data anggota ditemukan.', $data);
} else {
    $result = $conn->query("SELECT * FROM anggota ORDER BY id_anggota ASC");
    $rows   = $result->fetch_all(MYSQLI_ASSOC);
    response(200, 'success', 'Berhasil mengambil seluruh data anggota.', $rows);
}
