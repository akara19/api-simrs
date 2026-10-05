<?php
header('Access-Control-Allow-Origin: *');
require_once '../config/database.php';
require_once '../helpers/response.php';

$id     = $_GET['id'] ?? null;
$pdo    = getConnection();

if ($id) {
    $stmt   = $pdo->prepare("SELECT * FROM notifikasi WHERE nama_poli LIKE ?");
    $stmt->execute([$id]);
    $data   = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        sendResponse('error', 'Data notifikasi tidak ditemukan', null, 404);
    }
    sendResponse('success', 'Data notifikasi ditemukan', $data);
} else {
    $stmt   = $pdo->query("SELECT * FROM notifikasi ORDER BY id ASC");
    $data   = $stmt->fetchAll(PDO::FETCH_ASSOC);
    sendResponse('success', 'Daftar notifikasi berhasil diambil', $data);
}
