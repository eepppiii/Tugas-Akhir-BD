<?php
require_once '../includes/config.php';
$conn = getConnection();

$data = [];

// Total mahasiswa aktif
$r = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status='aktif'");
$data['total_mahasiswa'] = $r->fetch_assoc()['total'];

// Total mata kuliah
$r = $conn->query("SELECT COUNT(*) as total FROM mata_kuliah");
$data['total_mk'] = $r->fetch_assoc()['total'];

// Sesi aktif hari ini
$r = $conn->query("SELECT COUNT(*) as total FROM sesi_absensi WHERE status='aktif'");
$data['sesi_aktif'] = $r->fetch_assoc()['total'];

// Persentase kehadiran keseluruhan
$r = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as hadir FROM absensi");
$row = $r->fetch_assoc();
$data['pct_kehadiran'] = $row['total'] > 0 ? round(($row['hadir'] / $row['total']) * 100) : 0;

// Absensi terbaru (10 data)
$r = $conn->query("
    SELECT a.id, m.nim, m.nama, mk.nama_mk, s.tanggal, a.status, a.waktu_absen
    FROM absensi a
    JOIN mahasiswa m ON a.mahasiswa_id = m.id
    JOIN sesi_absensi s ON a.sesi_id = s.id
    JOIN mata_kuliah mk ON s.mk_id = mk.id
    ORDER BY a.created_at DESC
    LIMIT 10
");
$data['recent_absensi'] = $r->fetch_all(MYSQLI_ASSOC);

// Sesi aktif list
$r = $conn->query("
    SELECT s.id, s.kode_absen, s.tanggal, s.pertemuan_ke, mk.nama_mk
    FROM sesi_absensi s
    JOIN mata_kuliah mk ON s.mk_id = mk.id
    WHERE s.status='aktif'
    ORDER BY s.dibuka_pukul DESC
    LIMIT 5
");
$data['sesi_list'] = $r->fetch_all(MYSQLI_ASSOC);

jsonResponse(true, 'OK', $data);
