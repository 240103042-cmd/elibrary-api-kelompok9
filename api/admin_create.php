<?php
// ============================================================
// admin_create.php — Tambah admin baru
// POST /api/admin_create.php
// Body JSON: { username, password, nama_lengkap, email, level }
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(405, 'error', 'Hanya method POST yang diizinkan.');
}

$conn = getConnection();
$body = getRequestBody();

$required = ['username', 'password', 'nama_lengkap', 'email'];
foreach ($required as $field) {
    if (empty($body[$field])) {
        response(400, 'error', "Field '$field' wajib diisi.");
    }
}

$username     = trim($body['username']);
$password     = $body['password'];
$nama_lengkap = $body['nama_lengkap'];
$email        = $body['email'];
$level        = $body['level'] ?? 'admin';

// Validasi level
$allowed_levels = ['superadmin', 'admin', 'operator'];
if (!in_array($level, $allowed_levels)) {
    response(400, 'error', 'Level tidak valid. Pilihan: superadmin, admin, operator.');
}

// Cek username duplikat
$chkU = $conn->prepare("SELECT id_admin FROM admin WHERE username = ?");
$chkU->bind_param('s', $username);
$chkU->execute();
if ($chkU->get_result()->num_rows > 0) {
    response(409, 'error', 'Username sudah digunakan.');
}

// Cek email duplikat
$chkE = $conn->prepare("SELECT id_admin FROM admin WHERE email = ?");
$chkE->bind_param('s', $email);
$chkE->execute();
if ($chkE->get_result()->num_rows > 0) {
    response(409, 'error', 'Email sudah terdaftar.');
}

$hashed = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare(
    "INSERT INTO admin (username, password, nama_lengkap, email, level)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param('sssss', $username, $hashed, $nama_lengkap, $email, $level);

if ($stmt->execute()) {
    response(201, 'success', 'Admin berhasil ditambahkan.', ['id_admin' => $conn->insert_id]);
}
response(500, 'error', 'Gagal menambahkan admin.');
