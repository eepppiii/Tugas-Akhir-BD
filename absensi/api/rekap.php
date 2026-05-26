<?php
require_once '../includes/config.php';
$conn = getConnection();

$mk_id = intval($_GET['mk_id'] ?? 0);
$dari = $conn->real_escape_string($_GET['dari'] ?? '');
$sampai = $conn->real_escape_string($_GET['sampai'] ?? '');

$where = [];
if ($mk_id) $where[] = "s.mk_id = $mk_id";
if ($dari) $where[] = "s.tanggal >= '$dari'";
if ($sampai) $where[] = "s.tanggal <= '$sampai'";
$whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT
        m.id, m.nim, m.nama, m.semester,
        SUM(CASE WHEN a.status='hadir' THEN 1 ELSE 0 END) as hadir,
        SUM(CASE WHEN a.status='izin'  THEN 1 ELSE 0 END) as izin,
        SUM(CASE WHEN a.status='sakit' THEN 1 ELSE 0 END) as sakit,
        SUM(CASE WHEN a.status='alpha' THEN 1 ELSE 0 END) as alpha
    FROM mahasiswa m
    LEFT JOIN absensi a ON a.mahasiswa_id = m.id
    LEFT JOIN sesi_absensi s ON a.sesi_id = s.id
    $whereStr
    GROUP BY m.id
    ORDER BY m.nama ASC
";

$r = $conn->query($sql);
if (!$r) jsonResponse(false, 'Query error: ' . $conn->error);
jsonResponse(true, 'OK', $r->fetch_all(MYSQLI_ASSOC));
