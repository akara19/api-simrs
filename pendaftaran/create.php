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

$pasien_id = trim($input['pasien_id'] ?? '');
$jadwal_id   = trim($input['jadwal_id'] ?? '');
$tanggal_kunjungan = trim($input['tanggal_kunjungan'] ?? '');
$no_antrian = trim($input['no_antrian'] ?? '');
$status = trim($input['status'] ?? '');

if ($pasien_id === '') {
    sendResponse('error', 'Data pasien belum dipilih', null, 400);
}

try {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("INSERT INTO pendaftaran (pasien_id, jadwal_id, tanggal_kunjungan, no_antrian, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$pasien_id, $jadwal_id, $tanggal_kunjungan, $no_antrian, $status]);

    sendResponse('success', 'Data Pendaftaran Berhasil Ditambahkan', [
        'id'        => $pdo->lastInsertId(),
        'pasien_id' => $pasien_id
    ], 201);
} catch (PDOException $e) {
    sendResponse('error', 'Gagal menyimpan data', null, 500);
}
