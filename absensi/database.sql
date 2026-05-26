-- ============================================
-- SISTEM ABSENSI MAHASISWA
-- Database Setup Script
-- ============================================

CREATE DATABASE IF NOT EXISTS db_absensi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_absensi;

-- Tabel Jurusan
CREATE TABLE IF NOT EXISTS jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_jurusan VARCHAR(10) NOT NULL UNIQUE,
    nama_jurusan VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Mata Kuliah
CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_mk VARCHAR(10) NOT NULL UNIQUE,
    nama_mk VARCHAR(100) NOT NULL,
    sks INT NOT NULL DEFAULT 2,
    semester INT NOT NULL DEFAULT 1,
    jurusan_id INT,
    dosen VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE SET NULL
);

-- Tabel Mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    jurusan_id INT,
    semester INT NOT NULL DEFAULT 1,
    email VARCHAR(100),
    foto VARCHAR(200) DEFAULT 'default.png',
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE SET NULL
);

-- Tabel Jadwal
CREATE TABLE IF NOT EXISTS jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mk_id INT NOT NULL,
    hari ENUM('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    ruangan VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mk_id) REFERENCES mata_kuliah(id) ON DELETE CASCADE
);

-- Tabel Sesi Absensi
CREATE TABLE IF NOT EXISTS sesi_absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mk_id INT NOT NULL,
    tanggal DATE NOT NULL,
    pertemuan_ke INT NOT NULL DEFAULT 1,
    kode_absen VARCHAR(10) NOT NULL UNIQUE,
    status ENUM('aktif','tutup') DEFAULT 'aktif',
    dibuka_pukul DATETIME DEFAULT CURRENT_TIMESTAMP,
    ditutup_pukul DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mk_id) REFERENCES mata_kuliah(id) ON DELETE CASCADE
);

-- Tabel Absensi
CREATE TABLE IF NOT EXISTS absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesi_id INT NOT NULL,
    mahasiswa_id INT NOT NULL,
    status ENUM('hadir','izin','sakit','alpha') DEFAULT 'hadir',
    waktu_absen DATETIME DEFAULT CURRENT_TIMESTAMP,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_absensi (sesi_id, mahasiswa_id),
    FOREIGN KEY (sesi_id) REFERENCES sesi_absensi(id) ON DELETE CASCADE,
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
);

-- ============================================
-- DATA DUMMY
-- ============================================

INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES
('TI', 'Teknik Informatika'),
('SI', 'Sistem Informasi'),
('MI', 'Manajemen Informatika'),
('TK', 'Teknik Komputer');

INSERT INTO mata_kuliah (kode_mk, nama_mk, sks, semester, jurusan_id, dosen) VALUES
('TI101', 'Algoritma & Pemrograman', 3, 1, 1, 'Dr. Budi Santoso'),
('TI102', 'Matematika Diskrit', 3, 1, 1, 'Prof. Sari Dewi'),
('TI201', 'Struktur Data', 3, 2, 1, 'Dr. Ahmad Fauzi'),
('TI202', 'Basis Data', 3, 2, 1, 'Dra. Maya Putri'),
('SI101', 'Pengantar Sistem Informasi', 2, 1, 2, 'Dr. Rini Wulandari'),
('SI202', 'Analisis Sistem', 3, 2, 2, 'Prof. Hendra Wijaya');

INSERT INTO mahasiswa (nim, nama, jurusan_id, semester, email, status) VALUES
('2021001', 'Ahmad Rizki Pratama', 1, 3, 'ahmad.rizki@student.ac.id', 'aktif'),
('2021002', 'Siti Nurhaliza', 1, 3, 'siti.nur@student.ac.id', 'aktif'),
('2021003', 'Budi Setiawan', 1, 3, 'budi.s@student.ac.id', 'aktif'),
('2021004', 'Dewi Rahayu', 1, 3, 'dewi.r@student.ac.id', 'aktif'),
('2021005', 'Fajar Nugroho', 1, 3, 'fajar.n@student.ac.id', 'aktif'),
('2021006', 'Gita Puspita', 2, 3, 'gita.p@student.ac.id', 'aktif'),
('2021007', 'Hendra Saputra', 2, 3, 'hendra.s@student.ac.id', 'aktif'),
('2021008', 'Indah Permata', 1, 3, 'indah.p@student.ac.id', 'aktif'),
('2021009', 'Joko Susilo', 1, 3, 'joko.s@student.ac.id', 'nonaktif'),
('2021010', 'Kartika Sari', 2, 3, 'kartika.s@student.ac.id', 'aktif');

INSERT INTO sesi_absensi (mk_id, tanggal, pertemuan_ke, kode_absen, status) VALUES
(1, CURDATE(), 1, 'TI1234', 'aktif'),
(2, CURDATE(), 1, 'MA5678', 'tutup'),
(3, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 1, 'SD9012', 'tutup');

INSERT INTO absensi (sesi_id, mahasiswa_id, status, waktu_absen) VALUES
(1, 1, 'hadir', NOW()),
(1, 2, 'hadir', NOW()),
(1, 3, 'izin', NOW()),
(2, 1, 'hadir', NOW()),
(2, 2, 'sakit', NOW()),
(2, 4, 'hadir', NOW()),
(3, 1, 'hadir', NOW()),
(3, 3, 'hadir', NOW()),
(3, 5, 'alpha', NOW());
