<?php
require_once '../includes/config.php';
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(false, 'Method tidak valid');

$body = json_decode(file_get_contents('php://input'), true);
$nim = $conn->real_escape_string(trim($body['nim'] ?? ''));
$kode = strtoupper($conn->real_escape_string(trim($body['kode'] ?? '')));

if (!$nim || !$kode) jsonResponse(false, 'NIM dan kode harus diisi!');

// Cek mahasiswa
$r = $conn->query("SELECT id, nama FROM mahasiswa WHERE nim='$nim' AND status='aktif'");
if ($r->num_rows === 0) jsonResponse(false, 'NIM tidak ditemukan atau mahasiswa tidak aktif!');
$mhs = $r->fetch_assoc();

// Cek sesi aktif dengan kode tsb
$r = $conn->query("SELECT id, mk_id FROM sesi_absensi WHERE kode_absen='$kode' AND status='aktif'");
if ($r->num_rows === 0) jsonResponse(false, 'Kode absensi tidak valid atau sesi sudah ditutup!');
$sesi = $r->fetch_assoc();

// Cek sudah absen belum
$r = $conn->query("SELECT id FROM absensi WHERE sesi_id={$sesi['id']} AND mahasiswa_id={$mhs['id']}");
if ($r->num_rows > 0) jsonResponse(false, "Anda ({$mhs['nama']}) sudah tercatat hadir sebelumnya!");

// Insert absensi
$sql = "INSERT INTO absensi (sesi_id, mahasiswa_id, status, waktu_absen) VALUES ({$sesi['id']}, {$mhs['id']}, 'hadir', NOW())";
if ($conn->query($sql)) {
    jsonResponse(true, "✓ Absensi berhasil! Selamat datang, {$mhs['nama']}!");
} else {
    jsonResponse(false, 'Terjadi kesalahan: ' . $conn->error);
}
