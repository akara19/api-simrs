<?php
header('Access-Control-Allow-Origin: *');
require_once '../config/database.php';
require_once '../helpers/response.php';

$id     = $_GET['id'] ?? null;
$pdo    = getConnection();

if ($id) {
    //$stmt   = $pdo->prepare("SELECT * FROM dokter WHERE id = ?");
    $stmt   = $pdo->prepare("SELECT * FROM dokter WHERE nama_poli LIKE ?");
    $stmt->execute([$id]);
    $data   = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        sendResponse('error', 'Data dokter tidak ditemukan', null, 404);
    }
    sendResponse('success', 'Data dokter ditemukan', $data);
} else {
    $stmt   = $pdo->query("SELECT * FROM dokter ORDER BY id ASC");
    $data   = $stmt->fetchAll(PDO::FETCH_ASSOC);
    sendResponse('success', 'Daftar dokter berhasil diambil', $data);
}
