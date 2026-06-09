<?php
// ============================================================
// API PEMINJAMAN (BORROWING) — E-Library API | Kelompok 9
// Endpoint: /api/peminjaman
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
            "SELECT p.*, a.nama_lengkap, a.email, b.judul_buku, b.penulis
             FROM peminjaman p
             JOIN anggota a ON p.id_anggota = a.id_anggota
             JOIN buku    b ON p.id_buku    = b.id_buku
             WHERE p.id_pinjam = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        if (!$data) response(404, 'error', "Peminjaman ID $id tidak ditemukan.");
        response(200, 'success', 'Data peminjaman ditemukan.', $data);
    } else {
        $result = $conn->query(
            "SELECT p.*, a.nama_lengkap, a.email, b.judul_buku, b.penulis
             FROM peminjaman p
             JOIN anggota a ON p.id_anggota = a.id_anggota
             JOIN buku    b ON p.id_buku    = b.id_buku
             ORDER BY p.id_pinjam ASC"
        );
        response(200, 'success', 'Data peminjaman berhasil diambil.', $result->fetch_all(MYSQLI_ASSOC));
    }
}

// POST
if ($method === 'POST') {
    $body = getRequestBody();
    foreach (['id_anggota', 'id_buku', 'tanggal_pinjam'] as $f) {
        if (empty($body[$f])) response(400, 'error', "Field '$f' wajib diisi.");
    }

    $id_anggota = (int)$body['id_anggota'];
    $id_buku    = (int)$body['id_buku'];
    $tgl_pinjam = $body['tanggal_pinjam'];
    $tgl_kembali= $body['tanggal_kembali']  ?? null;
    $status     = $body['status_peminjaman'] ?? 'Dipinjam';

    $ca = $conn->prepare("SELECT id_anggota FROM anggota WHERE id_anggota = ?");
    $ca->bind_param('i', $id_anggota); $ca->execute();
    if ($ca->get_result()->num_rows === 0) response(404, 'error', "Anggota ID $id_anggota tidak ditemukan.");

    $cb = $conn->prepare("SELECT stok FROM buku WHERE id_buku = ?");
    $cb->bind_param('i', $id_buku); $cb->execute();
    $buku = $cb->get_result()->fetch_assoc();
    if (!$buku) response(404, 'error', "Buku ID $id_buku tidak ditemukan.");
    if ((int)$buku['stok'] <= 0) response(400, 'error', 'Stok buku habis.');

    $upd = $conn->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku = ?");
    $upd->bind_param('i', $id_buku); $upd->execute();

    $stmt = $conn->prepare(
        "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status_peminjaman)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('iisss', $id_anggota, $id_buku, $tgl_pinjam, $tgl_kembali, $status);
    if ($stmt->execute()) response(201, 'success', 'Peminjaman berhasil dicatat.', ['id_pinjam' => $conn->insert_id]);
    response(500, 'error', 'Gagal mencatat peminjaman.');
}

// PUT
if ($method === 'PUT') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');
    $body = getRequestBody();
    $chk  = $conn->prepare("SELECT * FROM peminjaman WHERE id_pinjam = ?");
    $chk->bind_param('i', $id); $chk->execute();
    $ex   = $chk->get_result()->fetch_assoc();
    if (!$ex) response(404, 'error', "Peminjaman ID $id tidak ditemukan.");

    $id_anggota = isset($body['id_anggota']) ? (int)$body['id_anggota'] : (int)$ex['id_anggota'];
    $id_buku    = isset($body['id_buku'])    ? (int)$body['id_buku']    : (int)$ex['id_buku'];
    $tgl_pinjam = $body['tanggal_pinjam']    ?? $ex['tanggal_pinjam'];
    $tgl_kembali= $body['tanggal_kembali']   ?? $ex['tanggal_kembali'];
    $status     = $body['status_peminjaman'] ?? $ex['status_peminjaman'];

    // Kembalikan stok bila status berubah menjadi 'Kembali'
    if ($status === 'Kembali' && $ex['status_peminjaman'] !== 'Kembali') {
        $rst = $conn->prepare("UPDATE buku SET stok = stok + 1 WHERE id_buku = ?");
        $rst->bind_param('i', $ex['id_buku']); $rst->execute();
    }

    $stmt = $conn->prepare(
        "UPDATE peminjaman SET id_anggota=?, id_buku=?, tanggal_pinjam=?,
         tanggal_kembali=?, status_peminjaman=? WHERE id_pinjam=?"
    );
    $stmt->bind_param('iisssi', $id_anggota, $id_buku, $tgl_pinjam, $tgl_kembali, $status, $id);
    if ($stmt->execute()) response(200, 'success', "Peminjaman ID $id berhasil diperbarui.");
    response(500, 'error', 'Gagal memperbarui peminjaman.');
}

// DELETE
if ($method === 'DELETE') {
    if (!$id) response(400, 'error', 'Parameter id diperlukan.');
    $chk = $conn->prepare("SELECT id_pinjam FROM peminjaman WHERE id_pinjam = ?");
    $chk->bind_param('i', $id); $chk->execute();
    if ($chk->get_result()->num_rows === 0) response(404, 'error', "Peminjaman ID $id tidak ditemukan.");
    $stmt = $conn->prepare("DELETE FROM peminjaman WHERE id_pinjam = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) response(200, 'success', "Peminjaman ID $id berhasil dihapus.");
    response(500, 'error', 'Gagal menghapus peminjaman.');
}

response(405, 'error', 'Method tidak diizinkan.');
