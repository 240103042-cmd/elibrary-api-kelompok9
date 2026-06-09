<?php
// ============================================================
// API KATEGORI (CATEGORY) — E-Library API | Kelompok 9
// Endpoint: /api/kategori
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

$conn   = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// ── GET ─────────────────────────────────────────────────────
if ($method === 'GET') {
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
}

// ── POST ─────────────────────────────────────────────────────
if ($method === 'POST') {
    $body = getRequestBody();

    if (empty($body['nama_kategori'])) {
        response(400, 'error', "Field 'nama_kategori' wajib diisi.");
    }

    $nama_kategori = $body['nama_kategori'];
    $deskripsi     = $body['deskripsi'] ?? null;

    $stmt = $conn->prepare(
        "INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)"
    );
    $stmt->bind_param('ss', $nama_kategori, $deskripsi);

    if ($stmt->execute()) {
        response(201, 'success', 'Kategori berhasil ditambahkan.', ['id_kategori' => $conn->insert_id]);
    }
    response(500, 'error', 'Gagal menambahkan kategori.');
}

// ── PUT ──────────────────────────────────────────────────────
if ($method === 'PUT') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');

    $body = getRequestBody();

    $check = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
    $check->bind_param('i', $id);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    if (!$existing) response(404, 'error', "Kategori dengan ID $id tidak ditemukan.");

    $nama_kategori = $body['nama_kategori'] ?? $existing['nama_kategori'];
    $deskripsi     = $body['deskripsi']     ?? $existing['deskripsi'];

    $stmt = $conn->prepare(
        "UPDATE kategori SET nama_kategori=?, deskripsi=? WHERE id_kategori=?"
    );
    $stmt->bind_param('ssi', $nama_kategori, $deskripsi, $id);

    if ($stmt->execute()) {
        response(200, 'success', "Kategori ID $id berhasil diperbarui.");
    }
    response(500, 'error', 'Gagal memperbarui kategori.');
}

// ── DELETE ───────────────────────────────────────────────────
if ($method === 'DELETE') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');

    $check = $conn->prepare("SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    $check->bind_param('i', $id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        response(404, 'error', "Kategori dengan ID $id tidak ditemukan.");
    }

    $stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        response(200, 'success', "Kategori ID $id berhasil dihapus.");
    }
    response(500, 'error', 'Gagal menghapus kategori.');
}

response(405, 'error', 'Method tidak diizinkan.');
