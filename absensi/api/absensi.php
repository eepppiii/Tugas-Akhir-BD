<?php
require_once '../includes/config.php';
$conn = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sesi_id = intval($_GET['sesi_id'] ?? 0);
    if (!$sesi_id) jsonResponse(false, 'sesi_id diperlukan');

    $r = $conn->query("
        SELECT a.id as absensi_id, m.nim, m.nama, a.status, a.waktu_absen, a.keterangan
        FROM absensi a
        JOIN mahasiswa m ON a.mahasiswa_id = m.id
        WHERE a.sesi_id = $sesi_id
        ORDER BY a.waktu_absen ASC
    ");
    jsonResponse(true, 'OK', $r->fetch_all(MYSQLI_ASSOC));
}

if ($method === 'PUT') {
    $id = intval($_GET['id'] ?? 0);
    $body = json_decode(file_get_contents('php://input'), true);
    $status = in_array($body['status'] ?? '', ['hadir','izin','sakit','alpha']) ? $body['status'] : 'hadir';
    if ($conn->query("UPDATE absensi SET status='$status' WHERE id=$id"))
        jsonResponse(true, 'Status diperbarui!');
    else jsonResponse(false, 'Gagal: ' . $conn->error);
}
