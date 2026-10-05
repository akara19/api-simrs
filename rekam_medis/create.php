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

$pendaftaran_id = trim($input['pendaftaran_id'] ?? '');
$dokter_id    = trim($input['dokter_id'] ?? '');
$diagnosa   =  trim($input['diagnosa'] ?? '');
$catatan   =  trim($input['catatan'] ?? '');
$tanggal   =  trim($input['tanggal'] ?? '');

if ($diagnosa === '') {
    sendResponse('error', 'Diagnosa wajib diisi', null, 400);
}

try {
    $pdo  = getConnection();
    $stmt = $pdo->prepare("INSERT INTO diagnosa (pendaftaran_id, dokter_id,diagnosa,catatan,tanggal) VALUES (?, ?,?,?,?)");
    $stmt->execute([$pendaftaran_id, $dokter_id, $diagnosa, $catatan, $tanggal]);

    sendResponse('success', 'Data rekam medis Berhasil Ditambahkan', [
        'id'        => $pdo->lastInsertId(),
        'diagnosa' => $diagnosa,
        'tanggal'    => $tanggal
    ], 201);
} catch (PDOException $e) {
    sendResponse('error', 'Gagal menyimpan data', null, 500);
}
