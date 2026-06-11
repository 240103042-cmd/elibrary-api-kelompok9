<?php
// ============================================================
// ROUTER — E-Library API | Kelompok 9
// Domain: pemrogmobile.infinityfree.me
// ============================================================

require_once __DIR__ . '/config/helpers.php';

setCorsHeaders();

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim(str_replace('/index.php', '', $uri), '/');
$parts  = explode('/', ltrim($uri, '/'));
$route  = $parts[1] ?? ($parts[0] ?? '');

// Inject ?id dari path segment jika ada (misal /api/anggota/5)
if (!isset($_GET['id']) && isset($parts[2]) && is_numeric($parts[2])) {
    $_GET['id'] = $parts[2];
}

switch ($route) {
    case 'anggota':
        require __DIR__ . '/api/anggota.php';
        break;
    case 'kategori':
        require __DIR__ . '/api/kategori.php';
        break;
    case 'buku':
        require __DIR__ . '/api/buku.php';
        break;
    case 'peminjaman':
        require __DIR__ . '/api/peminjaman.php';
        break;
    case 'file_digital':
        require __DIR__ . '/api/file_digital.php';
        break;
    case 'admin':
        require __DIR__ . '/api/admin.php';
        break;
    default:
        response(200, 'success', 'Selamat datang di E-Library API — Kelompok 9', [
            'kelompok'  => 9,
            'anggota'   => [
                ['nim' => '240103042', 'nama' => 'Burhan Yusuf Arifin'],
                ['nim' => '240103048', 'nama' => 'Arjuna Dwi Refa S'],
            ],
            'domain'    => 'pemrogmobile.infinityfree.me',
            'endpoints' => [
                'GET/POST'        => '/api/anggota',
                'GET/PUT/DELETE'  => '/api/anggota?id={id}',
                'GET/POST'        => '/api/kategori',
                'GET/PUT/DELETE'  => '/api/kategori?id={id}',
                'GET/POST'        => '/api/buku',
                'GET/PUT/DELETE'  => '/api/buku?id={id}',
                'GET/POST'        => '/api/peminjaman',
                'GET/PUT/DELETE'  => '/api/peminjaman?id={id}',
                'GET/POST'        => '/api/file_digital',
                'GET/PUT/DELETE'  => '/api/file_digital?id={id}',
                'GET/POST'        => '/api/admin',
                'GET/PUT/DELETE'  => '/api/admin?id={id}',
            ],
        ]);
}
