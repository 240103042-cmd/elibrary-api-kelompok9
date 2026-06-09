<?php
// ============================================================
// API FILE DIGITAL (E-BOOK FILE) — E-Library API | Kelompok 9
// Endpoint: /api/file_digital
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

setCorsHeaders();

$conn   = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// GET
if ($method === 'GET') {
    if ($id) {
        $stmt = $conn->prepare(
            "SELECT f.*, b.judul_buku, b.penulis
             FROM file_digital f
             JOIN buku b ON f.id_buku = b.id_buku
             WHERE f.id_file = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        if (!$data) response(404, 'error', "File Digital ID $id tidak ditemukan.");
        response(200, 'success', 'Data file digital ditemukan.', $data);
    } else {
        $result = $conn->query(
            "SELECT f.*, b.judul_buku, b.penulis
             FROM file_digital f
             JOIN buku b ON f.id_buku = b.id_buku
             ORDER BY f.id_file ASC"
        );
        response(200, 'success', 'Data file digital berhasil diambil.', $result->fetch_all(MYSQLI_ASSOC));
    }
}

// POST
if ($method === 'POST') {
    $body = getRequestBody();
    foreach (['id_buku', 'format_file', 'link_unduh'] as $f) {
        if (empty($body[$f])) response(400, 'error', "Field '$f' wajib diisi.");
    }

    $id_buku     = (int)$body['id_buku'];
    $format_file = strtoupper($body['format_file']);
    $ukuran_file = $body['ukuran_file'] ?? null;
    $link_unduh  = $body['link_unduh'];

    $cb = $conn->prepare("SELECT id_buku FROM buku WHERE id_buku = ?");
    $cb->bind_param('i', $id_buku); $cb->execute();
    if ($cb->get_result()->num_rows === 0) response(404, 'error', "Buku ID $id_buku tidak ditemukan.");

    $stmt = $conn->prepare(
        "INSERT INTO file_digital (id_buku, format_file, ukuran_file, link_unduh)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('isss', $id_buku, $format_file, $ukuran_file, $link_unduh);
    if ($stmt->execute()) response(201, 'success', 'File digital berhasil ditambahkan.', ['id_file' => $conn->insert_id]);
    response(500, 'error', 'Gagal menambahkan file digital.');
}

// PUT
if ($method === 'PUT') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');
    $body = getRequestBody();
    $chk  = $conn->prepare("SELECT * FROM file_digital WHERE id_file = ?");
    $chk->bind_param('i', $id); $chk->execute();
    $ex   = $chk->get_result()->fetch_assoc();
    if (!$ex) response(404, 'error', "File Digital ID $id tidak ditemukan.");

    $id_buku     = isset($body['id_buku'])    ? (int)$body['id_buku']                : (int)$ex['id_buku'];
    $format_file = isset($body['format_file']) ? strtoupper($body['format_file'])    : $ex['format_file'];
    $ukuran_file = $body['ukuran_file'] ?? $ex['ukuran_file'];
    $link_unduh  = $body['link_unduh']  ?? $ex['link_unduh'];

    $stmt = $conn->prepare(
        "UPDATE file_digital SET id_buku=?, format_file=?, ukuran_file=?, link_unduh=?
         WHERE id_file=?"
    );
    $stmt->bind_param('isssi', $id_buku, $format_file, $ukuran_file, $link_unduh, $id);
    if ($stmt->execute()) response(200, 'success', "File Digital ID $id berhasil diperbarui.");
    response(500, 'error', 'Gagal memperbarui file digital.');
}

// DELETE
if ($method === 'DELETE') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');
    $chk = $conn->prepare("SELECT id_file FROM file_digital WHERE id_file = ?");
    $chk->bind_param('i', $id); $chk->execute();
    if ($chk->get_result()->num_rows === 0) response(404, 'error', "File Digital ID $id tidak ditemukan.");
    $stmt = $conn->prepare("DELETE FROM file_digital WHERE id_file = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) response(200, 'success', "File Digital ID $id berhasil dihapus.");
    response(500, 'error', 'Gagal menghapus file digital.');
}

response(405, 'error', 'Method tidak diizinkan.');
