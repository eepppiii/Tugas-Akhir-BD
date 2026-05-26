<?php
require_once '../includes/config.php';
$conn = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

// GET: ambil semua mahasiswa
if ($method === 'GET') {
    $r = $conn->query("
        SELECT m.*, j.nama_jurusan
        FROM mahasiswa m
        LEFT JOIN jurusan j ON m.jurusan_id = j.id
        ORDER BY m.nama ASC
    ");
    jsonResponse(true, 'OK', $r->fetch_all(MYSQLI_ASSOC));
}

// POST: tambah mahasiswa
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $nim = $conn->real_escape_string($body['nim'] ?? '');
    $nama = $conn->real_escape_string($body['nama'] ?? '');
    $jurusan_id = intval($body['jurusan_id'] ?? 0);
    $semester = intval($body['semester'] ?? 1);
    $email = $conn->real_escape_string($body['email'] ?? '');
    $status = in_array($body['status'] ?? '', ['aktif','nonaktif']) ? $body['status'] : 'aktif';

    if (!$nim || !$nama) jsonResponse(false, 'NIM dan Nama wajib diisi!');

    $sql = "INSERT INTO mahasiswa (nim, nama, jurusan_id, semester, email, status) VALUES ('$nim','$nama'," . ($jurusan_id ?: 'NULL') . ",$semester,'$email','$status')";
    if ($conn->query($sql)) jsonResponse(true, 'Mahasiswa berhasil ditambahkan!');
    else jsonResponse(false, 'Gagal: ' . $conn->error);
}

// PUT: update mahasiswa
if ($method === 'PUT') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) jsonResponse(false, 'ID tidak valid');
    $body = json_decode(file_get_contents('php://input'), true);
    $nim = $conn->real_escape_string($body['nim'] ?? '');
    $nama = $conn->real_escape_string($body['nama'] ?? '');
    $jurusan_id = intval($body['jurusan_id'] ?? 0);
    $semester = intval($body['semester'] ?? 1);
    $email = $conn->real_escape_string($body['email'] ?? '');
    $status = in_array($body['status'] ?? '', ['aktif','nonaktif']) ? $body['status'] : 'aktif';

    $sql = "UPDATE mahasiswa SET nim='$nim', nama='$nama', jurusan_id=" . ($jurusan_id ?: 'NULL') . ", semester=$semester, email='$email', status='$status' WHERE id=$id";
    if ($conn->query($sql)) jsonResponse(true, 'Data mahasiswa diperbarui!');
    else jsonResponse(false, 'Gagal: ' . $conn->error);
}

// DELETE: hapus mahasiswa
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) jsonResponse(false, 'ID tidak valid');
    if ($conn->query("DELETE FROM mahasiswa WHERE id=$id")) jsonResponse(true, 'Mahasiswa dihapus!');
    else jsonResponse(false, 'Gagal: ' . $conn->error);
}
