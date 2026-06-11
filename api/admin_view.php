<?php
// ============================================================
// admin_view.php — Ambil data admin
// GET /api/admin_view.php          → semua admin
// GET /api/admin_view.php?id=1     → admin by ID
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
        "SELECT id_admin, username, nama_lengkap, email, level, created_at
         FROM admin WHERE id_admin = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) response(404, 'error', "Admin dengan ID $id tidak ditemukan.");
    response(200, 'success', 'Data admin ditemukan.', $data);
} else {
    $result = $conn->query(
        "SELECT id_admin, username, nama_lengkap, email, level, created_at
         FROM admin ORDER BY id_admin ASC"
    );
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    response(200, 'success', 'Berhasil mengambil seluruh data admin.', $rows);
}
