<?php
header('Access-Control-Allow-Origin: *');
require_once '../config/database.php';
require_once '../helpers/response.php';

$id     = $_GET['id'] ?? null;
$pdo    = getConnection();

if ($id) {
    //$stmt   = $pdo->prepare("SELECT * FROM poli WHERE id = ?");
    $stmt   = $pdo->prepare("SELECT * FROM rekam_medis WHERE pendaftaran_id LIKE ?");
    $stmt->execute([$id]);
    $data   = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        sendResponse('error', 'Data rekam medis tidak ditemukan', null, 404);
    }
    sendResponse('success', 'Data rekam medis ditemukan', $data);
} else {
    $stmt   = $pdo->query("SELECT * FROM rekam_medis ORDER BY id ASC");
    $data   = $stmt->fetchAll(PDO::FETCH_ASSOC);
    sendResponse('success', 'Daftar rekam medis berhasil diambil', $data);
}
