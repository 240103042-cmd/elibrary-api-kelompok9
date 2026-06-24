<?php
// ============================================================
// API BUKU (BOOK) — E-Library API | Kelompok 9
// Endpoint: /api/buku
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
        $stmt = $conn->prepare(
            "SELECT b.*, k.nama_kategori
             FROM buku b
             JOIN kategori k ON b.id_kategori = k.id_kategori
             WHERE b.id_buku = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();

        if (!$data) response(404, 'error', "Buku dengan ID $id tidak ditemukan.");
        response(200, 'success', 'Data buku ditemukan.', $data);
    } else {
        $result = $conn->query(
            "SELECT b.*, k.nama_kategori
             FROM buku b
             JOIN kategori k ON b.id_kategori = k.id_kategori
             ORDER BY b.id_buku ASC"
        );
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        response(200, 'success', 'Berhasil mengambil seluruh data buku.', $rows);
    }
}

// ── POST ─────────────────────────────────────────────────────
if ($method === 'POST') {
    $body = getRequestBody();

    $required = ['id_kategori', 'judul_buku', 'penulis'];
    foreach ($required as $field) {
        if (empty($body[$field])) {
            response(400, 'error', "Field '$field' wajib diisi.");
        }
    }

    $id_kategori  = (int)$body['id_kategori'];
    $judul_buku   = $body['judul_buku'];
    $penulis      = $body['penulis'];
    $penerbit     = $body['penerbit']     ?? null;
    $tahun_terbit = isset($body['tahun_terbit']) ? (int)$body['tahun_terbit'] : null;
    $stok         = isset($body['stok'])         ? (int)$body['stok']         : 0;

    // Validasi id_kategori ada
    $kat = $conn->prepare("SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    $kat->bind_param('i', $id_kategori);
    $kat->execute();
    if ($kat->get_result()->num_rows === 0) {
        response(404, 'error', "Kategori dengan ID $id_kategori tidak ditemukan.");
    }

    $stmt = $conn->prepare(
        "INSERT INTO buku (id_kategori, judul_buku, penulis, penerbit, tahun_terbit, stok)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('isssii', $id_kategori, $judul_buku, $penulis, $penerbit, $tahun_terbit, $stok);

    if ($stmt->execute()) {
        response(201, 'success', 'Buku berhasil ditambahkan.', ['id_buku' => $conn->insert_id]);
    }
    response(500, 'error', 'Gagal menambahkan buku.');
}

// ── PUT ──────────────────────────────────────────────────────
if ($method === 'PUT') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');

    $body = getRequestBody();

    $check = $conn->prepare("SELECT * FROM buku WHERE id_buku = ?");
    $check->bind_param('i', $id);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    if (!$existing) response(404, 'error', "Buku dengan ID $id tidak ditemukan.");

    $id_kategori  = isset($body['id_kategori'])  ? (int)$body['id_kategori']  : (int)$existing['id_kategori'];
    $judul_buku   = $body['judul_buku']   ?? $existing['judul_buku'];
    $penulis      = $body['penulis']      ?? $existing['penulis'];
    $penerbit     = $body['penerbit']     ?? $existing['penerbit'];
    $tahun_terbit = isset($body['tahun_terbit']) ? (int)$body['tahun_terbit'] : (int)$existing['tahun_terbit'];
    $stok         = isset($body['stok'])         ? (int)$body['stok']         : (int)$existing['stok'];

    $stmt = $conn->prepare(
        "UPDATE buku SET id_kategori=?, judul_buku=?, penulis=?, penerbit=?,
         tahun_terbit=?, stok=? WHERE id_buku=?"
    );
    $stmt->bind_param('isssiii', $id_kategori, $judul_buku, $penulis, $penerbit, $tahun_terbit, $stok, $id);

    if ($stmt->execute()) {
        response(200, 'success', "Buku ID $id berhasil diperbarui.");
    }
    response(500, 'error', 'Gagal memperbarui buku.');
}

// ── DELETE ───────────────────────────────────────────────────
if ($method === 'DELETE') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');

    $check = $conn->prepare("SELECT id_buku FROM buku WHERE id_buku = ?");
    $check->bind_param('i', $id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        response(404, 'error', "Buku dengan ID $id tidak ditemukan.");
    }

    $stmt = $conn->prepare("DELETE FROM buku WHERE id_buku = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        response(200, 'success', "Buku ID $id berhasil dihapus.");
    }
    response(500, 'error', 'Gagal menghapus buku.');
}

response(405, 'error', 'Method tidak diizinkan.');

// [Arjuna Dwi Refa S] Merapikan struktur response JSON
