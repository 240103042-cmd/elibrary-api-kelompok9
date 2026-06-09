<?php
// ============================================================
// CORS & Response Helper — E-Library API
// ============================================================

function setCorsHeaders(): void {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Content-Type: application/json; charset=UTF-8');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

function response(int $code, string $status, string $message, $data = null): void {
    http_response_code($code);
    $payload = [
        'status'  => $status,
        'message' => $message,
    ];
    if ($data !== null) {
        $payload['data'] = $data;
    }
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

function getRequestBody(): array {
    $raw = file_get_contents('php://input');
    if (empty($raw)) {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}
