# SiAbsen — Sistem Absensi Mahasiswa
**Stack:** PHP · MySQL · HTML · CSS · JavaScript (Vanilla)

---

## 📁 Struktur Folder

```
absensi/
├── index.php               ← Halaman utama (SPA)
├── database.sql            ← Script buat database & data dummy
├── includes/
│   └── config.php          ← Konfigurasi database & helper
├── api/
│   ├── dashboard.php       ← Statistik dashboard
│   ├── mahasiswa.php       ← CRUD mahasiswa
│   ├── matakuliah.php      ← CRUD mata kuliah
│   ├── jurusan.php         ← Daftar jurusan
│   ├── sesi.php            ← Buka/tutup sesi absensi
│   ├── absensi.php         ← Detail absensi per sesi
│   ├── scan.php            ← Absensi mandiri (input kode)
│   └── rekap.php           ← Rekap kehadiran
└── assets/
    ├── css/style.css       ← Stylesheet utama
    └── js/app.js           ← JavaScript utama
```

---

## 🚀 Cara Instalasi

### 1. Persyaratan
- PHP >= 7.4
- MySQL / MariaDB
- Web Server (Apache/Nginx) atau XAMPP/Laragon

### 2. Setup Database
```sql
-- Import file database.sql ke MySQL:
mysql -u root -p < database.sql

-- Atau buka phpMyAdmin dan import file database.sql
```

### 3. Konfigurasi Koneksi
Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');  // host database
define('DB_USER', 'root');       // username MySQL
define('DB_PASS', '');           // password MySQL
define('DB_NAME', 'db_absensi'); // nama database
```

### 4. Jalankan Aplikasi
- Letakkan folder `absensi/` di `htdocs/` (XAMPP) atau `www/` (Laragon)
- Akses: `http://localhost/absensi/`

---

## ✨ Fitur

| Fitur | Keterangan |
|-------|-----------|
| 📊 Dashboard | Statistik total mahasiswa, MK, sesi aktif, rata-rata kehadiran |
| 👥 Manajemen Mahasiswa | CRUD data mahasiswa dengan filter & pencarian |
| 📚 Manajemen Mata Kuliah | CRUD mata kuliah dengan jurusan & dosen |
| 📅 Sesi Absensi | Buka sesi, generate kode otomatis, tutup sesi |
| ✅ Absen Mandiri | Mahasiswa input NIM + kode absen |
| 📈 Rekap Kehadiran | Laporan persentase kehadiran per mahasiswa |
| 🔔 Toast Notification | Notifikasi sukses/error real-time |
| 📱 Responsive | Mendukung tampilan mobile |

---

## 🔄 Alur Penggunaan

1. **Dosen** membuka sesi absensi → sistem generate kode (misal: `TI4F2A`)
2. **Dosen** membagikan kode kepada mahasiswa
3. **Mahasiswa** membuka halaman "Absen Mandiri", masukkan NIM + kode
4. Sistem mencatat kehadiran secara otomatis
5. **Dosen** bisa melihat detail absensi dan mengubah status (hadir/izin/sakit/alpha)
6. Rekap kehadiran bisa difilter per mata kuliah dan rentang tanggal

---

## 🛡️ Catatan Keamanan
- Tambahkan autentikasi (login) untuk produksi
- Gunakan prepared statements untuk semua query (sudah sebagian)
- Aktifkan HTTPS di server produksi
- Batasi akses API dengan session/token
