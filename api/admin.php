<?php
// ============================================================
// API ADMIN — E-Library API | Kelompok 9
// Endpoint: /api/admin
// Methods : GET, POST, PUT, DELETE
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

$conn   = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// ── GET ──────────────────────────────────────────────────────
if ($method === 'GET') {
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
}

// ── POST ─────────────────────────────────────────────────────
if ($method === 'POST') {
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
        response(400, 'error', "Level tidak valid. Pilihan: superadmin, admin, operator.");
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
}

// ── PUT ──────────────────────────────────────────────────────
if ($method === 'PUT') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');

    $body = getRequestBody();

    $check = $conn->prepare("SELECT * FROM admin WHERE id_admin = ?");
    $check->bind_param('i', $id);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    if (!$existing) response(404, 'error', "Admin dengan ID $id tidak ditemukan.");

    $username     = trim($body['username'])     ?? $existing['username'];
    $nama_lengkap = $body['nama_lengkap']       ?? $existing['nama_lengkap'];
    $email        = $body['email']              ?? $existing['email'];
    $level        = $body['level']              ?? $existing['level'];

    // Validasi level jika dikirim
    if (isset($body['level'])) {
        $allowed_levels = ['superadmin', 'admin', 'operator'];
        if (!in_array($level, $allowed_levels)) {
            response(400, 'error', "Level tidak valid. Pilihan: superadmin, admin, operator.");
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

    // Update password jika dikirim
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
}

// ── DELETE ───────────────────────────────────────────────────
if ($method === 'DELETE') {
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
}

response(405, 'error', 'Method tidak diizinkan.');
