<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_absensi');

// Koneksi Database
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . $conn->connect_error]));
    }
    $conn->set_charset("utf8");
    return $conn;
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: Response JSON
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) $response['data'] = $data;
    echo json_encode($response);
    exit;
}

// Helper: Format tanggal Indonesia
function formatTanggal($date) {
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $d = date('d', strtotime($date));
    $m = date('n', strtotime($date));
    $y = date('Y', strtotime($date));
    return "$d {$bulan[$m]} $y";
}
?>
