<?php
// ============================================================
// DATABASE CONFIG — E-Library API | Kelompok 9
// Membaca dari environment variable (Vercel) atau fallback
// ke nilai default (lokal / InfinityFree)
// ============================================================

define('DB_HOST', getenv('DB_HOST') ?: 'sql209.infinityfree.com');
define('DB_USER', getenv('DB_USER') ?: 'if0_41598099');
define('DB_PASS', getenv('DB_PASS') ?: 'A3726b356');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_41598099_elibrary');
define('DB_PORT', getenv('DB_PORT') ?: 3306);

function getConnection(): mysqli {
    $conn = mysqli_init();
    
    // Untuk Aiven / Vercel SSL Requirement
    $conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
    
    // Gunakan real_connect untuk custom port dan SSL
    @$conn->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, NULL, MYSQLI_CLIENT_SSL);

    if ($conn->connect_error) {
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi database gagal: ' . $conn->connect_error
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
