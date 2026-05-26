<?php
require_once '../includes/config.php';
$conn = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $r = $conn->query("
        SELECT s.*, mk.nama_mk, mk.kode_mk
        FROM sesi_absensi s
        JOIN mata_kuliah mk ON s.mk_id = mk.id
        ORDER BY s.created_at DESC
        LIMIT 50
    ");
    jsonResponse(true, 'OK', $r->fetch_all(MYSQLI_ASSOC));
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $mk_id = intval($body['mk_id'] ?? 0);
    $tanggal = $conn->real_escape_string($body['tanggal'] ?? date('Y-m-d'));
    $pertemuan_ke = intval($body['pertemuan_ke'] ?? 1);

    if (!$mk_id) jsonResponse(false, 'Pilih mata kuliah!');

    // Generate kode unik 6 karakter
    do {
        $kode = strtoupper(substr(md5(uniqid()), 0, 6));
        $cek = $conn->query("SELECT id FROM sesi_absensi WHERE kode_absen='$kode'");
    } while ($cek->num_rows > 0);

    $sql = "INSERT INTO sesi_absensi (mk_id, tanggal, pertemuan_ke, kode_absen, status) VALUES ($mk_id,'$tanggal',$pertemuan_ke,'$kode','aktif')";
    if ($conn->query($sql)) {
        jsonResponse(true, 'Sesi absensi berhasil dibuka!', ['kode_absen' => $kode, 'id' => $conn->insert_id]);
    } else {
        jsonResponse(false, 'Gagal: ' . $conn->error);
    }
}

if ($method === 'PUT') {
    $id = intval($_GET['id'] ?? 0);
    $body = json_decode(file_get_contents('php://input'), true);
    $status = in_array($body['status'] ?? '', ['aktif','tutup']) ? $body['status'] : 'tutup';
    $ditutup = $status === 'tutup' ? ", ditutup_pukul=NOW()" : '';
    if ($conn->query("UPDATE sesi_absensi SET status='$status'$ditutup WHERE id=$id"))
        jsonResponse(true, 'Status sesi diperbarui!');
    else jsonResponse(false, 'Gagal: ' . $conn->error);
}
