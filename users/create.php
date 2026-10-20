<?php
ini_set('display_errors', 0);   // error PHP tidak boleh bocor sebagai HTML
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

require_once '../config/database.php';
require_once '../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method harus POST', null, 405);
}

$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST;
}

$nama     = trim($input['nama'] ?? '');
$email    = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');
$role     = trim($input['role'] ?? '');

if ($nama === '') {
    sendResponse('error', 'Nama wajib diisi', null, 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse('error', 'Format email tidak valid', null, 400);
}
if (strlen($password) < 6) {
    sendResponse('error', 'Password minimal 6 karakter', null, 400);
}
if (!in_array($role, ['admin', 'dokter', 'pasien'], true)) {
    $role = 'pasien'; // default
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$api_token     = bin2hex(random_bytes(32)); // 64 karakter, unik

try {
    $pdo  = getConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO users (nama, email, password_hash, role, api_token)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([$nama, $email, $password_hash, $role, $api_token]);

    sendResponse('success', 'Data users berhasil ditambahkan', [
        'id'   => $pdo->lastInsertId(),
        'nama' => $nama,
        'role' => $role,
    ], 201);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') { // duplicate email
        sendResponse('error', 'Email sudah terdaftar', null, 409);
    }
    sendResponse('error', 'Gagal menyimpan data', null, 500);
}
