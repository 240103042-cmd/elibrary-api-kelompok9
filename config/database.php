<?php

define('DB_HOST', 'sql209.infinityfree.com');
define('DB_USER', 'if0_41598099');         
define('DB_PASS', 'A3726b356');        
define('DB_NAME', 'if0_41598099_elibrary');    

function getConnection(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi database gagal: ' . $conn->connect_error
        ]);
        exit();
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
