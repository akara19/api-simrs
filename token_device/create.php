<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

require_once '../config/database.php';
require_once '../helpers/response.php';

// Preflight CORS: balas lalu hentikan eksekusi
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Tolak semua method selain POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method harus POST', null, 405);
}

// Ambil input: JSON body dulu, fallback ke form-data
$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST;
}

$user_id = trim($input['user_id'] ?? '');
$fcm_token    = trim($input['fcm_token'] ?? '');

if ($fcm_token === '') {
    sendResponse('error', 'Token wajib diisi', null, 400);
}

try {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("INSERT INTO token_device (user_id, fcm_token) VALUES (?, ?)");
    $stmt->execute([$user_id, $fcm_token]);

    sendResponse('success', 'Data Token Berhasil Ditambahkan', [
        'id'        => $pdo->lastInsertId(),
        'user_id' => $user_id,
        'fcm_token'    => $fcm_token
    ], 201);
} catch (PDOException $e) {
    sendResponse('error', 'Gagal menyimpan data', null, 500);
}
