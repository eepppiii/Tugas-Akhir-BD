<?php
require_once '../includes/config.php';
$conn = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $r = $conn->query("
        SELECT mk.*, j.nama_jurusan
        FROM mata_kuliah mk
        LEFT JOIN jurusan j ON mk.jurusan_id = j.id
        ORDER BY mk.kode_mk ASC
    ");
    jsonResponse(true, 'OK', $r->fetch_all(MYSQLI_ASSOC));
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $kode = strtoupper($conn->real_escape_string($body['kode_mk'] ?? ''));
    $nama = $conn->real_escape_string($body['nama_mk'] ?? '');
    $sks = intval($body['sks'] ?? 2);
    $semester = intval($body['semester'] ?? 1);
    $jurusan_id = intval($body['jurusan_id'] ?? 0);
    $dosen = $conn->real_escape_string($body['dosen'] ?? '');

    if (!$kode || !$nama) jsonResponse(false, 'Kode dan Nama MK wajib diisi!');

    $sql = "INSERT INTO mata_kuliah (kode_mk, nama_mk, sks, semester, jurusan_id, dosen) VALUES ('$kode','$nama',$sks,$semester," . ($jurusan_id ?: 'NULL') . ",'$dosen')";
    if ($conn->query($sql)) jsonResponse(true, 'Mata kuliah berhasil ditambahkan!');
    else jsonResponse(false, 'Gagal: ' . $conn->error);
}

if ($method === 'PUT') {
    $id = intval($_GET['id'] ?? 0);
    $body = json_decode(file_get_contents('php://input'), true);
    $kode = strtoupper($conn->real_escape_string($body['kode_mk'] ?? ''));
    $nama = $conn->real_escape_string($body['nama_mk'] ?? '');
    $sks = intval($body['sks'] ?? 2);
    $semester = intval($body['semester'] ?? 1);
    $jurusan_id = intval($body['jurusan_id'] ?? 0);
    $dosen = $conn->real_escape_string($body['dosen'] ?? '');

    $sql = "UPDATE mata_kuliah SET kode_mk='$kode', nama_mk='$nama', sks=$sks, semester=$semester, jurusan_id=" . ($jurusan_id ?: 'NULL') . ", dosen='$dosen' WHERE id=$id";
    if ($conn->query($sql)) jsonResponse(true, 'Mata kuliah diperbarui!');
    else jsonResponse(false, 'Gagal: ' . $conn->error);
}

if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if ($conn->query("DELETE FROM mata_kuliah WHERE id=$id")) jsonResponse(true, 'Mata kuliah dihapus!');
    else jsonResponse(false, 'Gagal: ' . $conn->error);
}
