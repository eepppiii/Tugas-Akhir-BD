<?php
require_once '../includes/config.php';
$conn = getConnection();
$r = $conn->query("SELECT * FROM jurusan ORDER BY nama_jurusan");
jsonResponse(true, 'OK', $r->fetch_all(MYSQLI_ASSOC));
