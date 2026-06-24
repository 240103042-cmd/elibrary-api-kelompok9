<?php
// ============================================================
// API ANGGOTA (MEMBER) — E-Library API | Kelompok 9
// Endpoint: /api/anggota
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

$conn   = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// ── GET: Ambil semua anggota atau satu anggota by ID ─────────
if ($method === 'GET') {
    if ($id) {
        $stmt = $conn->prepare("SELECT * FROM anggota WHERE id_anggota = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data   = $result->fetch_assoc();

        if (!$data) {
            response(404, 'error', "Anggota dengan ID $id tidak ditemukan.");
        }
        response(200, 'success', 'Data anggota ditemukan.', $data);
    } else {
        $result = $conn->query("SELECT * FROM anggota ORDER BY id_anggota ASC");
        $rows   = $result->fetch_all(MYSQLI_ASSOC);
        response(200, 'success', 'Berhasil mengambil seluruh data anggota.', $rows);
    }
}

// ── POST: Tambah anggota baru ────────────────────────────────
if ($method === 'POST') {
    $body = getRequestBody();

    $required = ['nama_lengkap', 'email', 'tanggal_daftar'];
    foreach ($required as $field) {
        if (empty($body[$field])) {
            response(400, 'error', "Field '$field' wajib diisi.");
        }
    }

    $nama           = $body['nama_lengkap'];
    $email          = $body['email'];
    $no_telepon     = $body['no_telepon']    ?? null;
    $tanggal_daftar = $body['tanggal_daftar'];

    // Cek email duplikat
    $check = $conn->prepare("SELECT id_anggota FROM anggota WHERE email = ?");
    $check->bind_param('s', $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        response(409, 'error', 'Email sudah terdaftar.');
    }

    $stmt = $conn->prepare(
        "INSERT INTO anggota (nama_lengkap, email, no_telepon, tanggal_daftar)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('ssss', $nama, $email, $no_telepon, $tanggal_daftar);

    if ($stmt->execute()) {
        $newId = $conn->insert_id;
        response(201, 'success', 'Anggota berhasil ditambahkan.', ['id_anggota' => $newId]);
    }
    response(500, 'error', 'Gagal menambahkan anggota.');
}

// ── PUT: Update anggota by ID ────────────────────────────────
if ($method === 'PUT') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');

    $body = getRequestBody();

    // Pastikan data lama ada
    $check = $conn->prepare("SELECT * FROM anggota WHERE id_anggota = ?");
    $check->bind_param('i', $id);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    if (!$existing) response(404, 'error', "Anggota dengan ID $id tidak ditemukan.");

    $nama           = $body['nama_lengkap']   ?? $existing['nama_lengkap'];
    $email          = $body['email']           ?? $existing['email'];
    $no_telepon     = $body['no_telepon']      ?? $existing['no_telepon'];
    $tanggal_daftar = $body['tanggal_daftar']  ?? $existing['tanggal_daftar'];

    $stmt = $conn->prepare(
        "UPDATE anggota SET nama_lengkap=?, email=?, no_telepon=?, tanggal_daftar=?
         WHERE id_anggota=?"
    );
    $stmt->bind_param('ssssi', $nama, $email, $no_telepon, $tanggal_daftar, $id);

    if ($stmt->execute()) {
        response(200, 'success', "Anggota ID $id berhasil diperbarui.");
    }
    response(500, 'error', 'Gagal memperbarui anggota.');
}

// ── DELETE: Hapus anggota by ID ───────────────────────────────
if ($method === 'DELETE') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');

    $check = $conn->prepare("SELECT id_anggota FROM anggota WHERE id_anggota = ?");
    $check->bind_param('i', $id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        response(404, 'error', "Anggota dengan ID $id tidak ditemukan.");
    }

    $stmt = $conn->prepare("DELETE FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        response(200, 'success', "Anggota ID $id berhasil dihapus.");
    }
    response(500, 'error', 'Gagal menghapus anggota.');
}

response(405, 'error', 'Method tidak diizinkan.');

// [Burhan Yusuf Arifin] Optimasi query pengambilan anggota
