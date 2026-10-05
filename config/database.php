<?php
function getConnection()
{
    $host = '127.0.0.1:3307';
    $dbname = 'api_rs';
    $user = 'root';
    $pass = '1986';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal']);
        exit;
    }
}
