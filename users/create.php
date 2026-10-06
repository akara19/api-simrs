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

$nama           = trim($input['nama'] ?? '');
$email          = trim($input['email'] ?? '');
$password_hash  = trim(password_hash($input['password_hash'] ?? ''), PASSWORD_DEFAULT);
$role           = trim($input['role'] ?? '');
$api_token      = trim($input['api_token'] ?? '');

if ($nama === '') {
    sendResponse('error', 'Nama wajib diisi', null, 400);
}

try {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("INSERT INTO users (nama, email,password_hash,role,api_token) VALUES (?, ?,?,?,?)");
    $stmt->execute([$nama, $email,$password_hash,$role,$api_token]);

    sendResponse('success', 'Data users Berhasil Ditambahkan', [
        'id'        => $pdo->lastInsertId(),
        'nama' => $user_id
    ], 201);
} catch (PDOException $e) {
    sendResponse('error', 'Gagal menyimpan data', null, 500);
}
