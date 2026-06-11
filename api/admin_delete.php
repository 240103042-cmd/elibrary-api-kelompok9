<?php
// ============================================================
// admin_delete.php — Hapus data admin
// DELETE /api/admin_delete.php?id=1
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    response(405, 'error', 'Hanya method DELETE yang diizinkan.');
}

$conn = getConnection();
$id   = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) response(400, 'error', 'Parameter id diperlukan.');

$check = $conn->prepare("SELECT id_admin FROM admin WHERE id_admin = ?");
$check->bind_param('i', $id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    response(404, 'error', "Admin dengan ID $id tidak ditemukan.");
}

$stmt = $conn->prepare("DELETE FROM admin WHERE id_admin = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    response(200, 'success', "Admin ID $id berhasil dihapus.");
}
response(500, 'error', 'Gagal menghapus admin.');
