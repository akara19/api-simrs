<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

require_once '../config/database.php';
require_once '../helpers/response.php';

// Menangani Preflight Request dari Klien Mobile/Web
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Menangkap payload berformat JSON
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

$id        = $data['id'] ?? $_POST['id'] ?? null;
$user_id   = $data['user_id'] ?? $_POST['user_id'] ?? null;
$fcm_token = $data['fcm_token'] ?? $_POST['fcm_token'] ?? null;

// Validasi kelengkapan parameter esensial
if (empty($id) || empty($user_id) || empty($fcm_token)) {
    sendResponse('error', 'Id, User ID, dan FCM Token wajib diisi', null, 400);
}

$pdo = getConnection();

try {
    // 1. Validasi Integritas Kunci Tamu (Foreign Key)
    // Memastikan user_id yang dikirim benar-benar ada di tabel induk (users)
    $cekUser = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $cekUser->execute([$user_id]);
    if (!$cekUser->fetch()) {
        sendResponse('error', 'Integritas gagal: User ID tidak ditemukan pada data master', null, 404);
    }

    // 2. Validasi Eksistensi Data Target
    $cekToken = $pdo->prepare("SELECT id FROM token_device WHERE id = ?");
    $cekToken->execute([$id]);
    if (!$cekToken->fetch()) {
        sendResponse('error', 'Data token device tidak ditemukan', null, 404);
    }

    // 3. Eksekusi Pembaruan Data (Mutasi)
    $stmt = $pdo->prepare("UPDATE token_device SET user_id = ?, fcm_token = ? WHERE id = ?");
    $stmt->execute([$user_id, $fcm_token, $id]);

    sendResponse('success', 'FCM Token berhasil diperbarui', [
        'id'        => $id,
        'user_id'   => $user_id,
        'fcm_token' => $fcm_token
    ], 200);

} catch (PDOException $e) {
    sendResponse('error', 'Kesalahan Server: ' . $e->getMessage(), null, 500);
}