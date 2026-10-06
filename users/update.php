<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

require_once '../config/database.php';
require_once '../helpers/response.php';

// Menangkap metode OPTIONS untuk preflight request pada aplikasi mobile/frontend
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Mengambil data dari body request berformat JSON (Standar RESTful)
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// Fallback ke $_POST jika klien mengirimkan form-data konvensional
$id    = $data['id'] ?? $_POST['id'] ?? null;
$nama  = $data['nama'] ?? $_POST['nama'] ?? null;
$email = $data['email'] ?? $_POST['email'] ?? null;
$role  = $data['role'] ?? $_POST['role'] ?? null;

// Validasi kelengkapan data
if (empty($id) || empty($nama) || empty($email) || empty($role)) {
    sendResponse('error', 'Id, Nama, Email, dan Role wajib diisi', null, 400);
}

// Validasi enum role berdasarkan skema database
$allowed_roles = ['admin', 'dokter', 'pasien'];
if (!in_array($role, $allowed_roles)) {
    sendResponse('error', 'Role tidak valid. Gunakan: admin, dokter, atau pasien', null, 400);
}

$pdo = getConnection();

try {
    // Pengecekan eksistensi data user
    $cek = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $cek->execute([$id]);
    if (!$cek->fetch()) {
        sendResponse('error', 'Data user tidak ditemukan', null, 404);
    }

    // Pengecekan duplikasi email untuk menjaga integritas data (kolom is_unique)
    $cekEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $cekEmail->execute([$email, $id]);
    if ($cekEmail->fetch()) {
        sendResponse('error', 'Email sudah terdaftar pada pengguna lain', null, 409);
    }

    // Eksekusi pembaruan data
    $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?");
    $stmt->execute([$nama, $email, $role, $id]);

    sendResponse('success', 'Data User berhasil diperbarui', [
        'id'    => $id,
        'nama'  => $nama,
        'email' => $email,
        'role'  => $role
    ], 200);

} catch (PDOException $e) {
    // Penanganan eksepsi level server
    sendResponse('error', 'Terjadi kesalahan pada server: ' . $e->getMessage(), null, 500);
}