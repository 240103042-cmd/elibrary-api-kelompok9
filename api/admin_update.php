<?php
// ============================================================
// admin_update.php — Update data admin
// PUT /api/admin_update.php?id=1
// Body JSON: { username?, password?, nama_lengkap?, email?, level? }
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    response(405, 'error', 'Hanya method PUT yang diizinkan.');
}

$conn = getConnection();
$id   = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) response(400, 'error', 'Parameter id diperlukan.');

$body = getRequestBody();

// Ambil data lama
$check = $conn->prepare("SELECT * FROM admin WHERE id_admin = ?");
$check->bind_param('i', $id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
if (!$existing) response(404, 'error', "Admin dengan ID $id tidak ditemukan.");

$username     = isset($body['username'])     ? trim($body['username'])   : $existing['username'];
$nama_lengkap = $body['nama_lengkap']        ?? $existing['nama_lengkap'];
$email        = $body['email']               ?? $existing['email'];
$level        = $body['level']               ?? $existing['level'];

// Validasi level jika dikirim
if (isset($body['level'])) {
    $allowed_levels = ['superadmin', 'admin', 'operator'];
    if (!in_array($level, $allowed_levels)) {
        response(400, 'error', 'Level tidak valid. Pilihan: superadmin, admin, operator.');
    }
}

// Cek username duplikat (kecuali diri sendiri)
if (isset($body['username'])) {
    $chkU = $conn->prepare("SELECT id_admin FROM admin WHERE username = ? AND id_admin != ?");
    $chkU->bind_param('si', $username, $id);
    $chkU->execute();
    if ($chkU->get_result()->num_rows > 0) {
        response(409, 'error', 'Username sudah digunakan.');
    }
}

// Update dengan atau tanpa password baru
if (!empty($body['password'])) {
    $hashed = password_hash($body['password'], PASSWORD_BCRYPT);
    $stmt = $conn->prepare(
        "UPDATE admin SET username=?, password=?, nama_lengkap=?, email=?, level=?
         WHERE id_admin=?"
    );
    $stmt->bind_param('sssssi', $username, $hashed, $nama_lengkap, $email, $level, $id);
} else {
    $stmt = $conn->prepare(
        "UPDATE admin SET username=?, nama_lengkap=?, email=?, level=?
         WHERE id_admin=?"
    );
    $stmt->bind_param('ssssi', $username, $nama_lengkap, $email, $level, $id);
}

if ($stmt->execute()) {
    response(200, 'success', "Admin ID $id berhasil diperbarui.");
}
response(500, 'error', 'Gagal memperbarui admin.');
