<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiAbsen — Sistem Absensi Mahasiswa</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app-wrapper">
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">🎓</div>
            <h1>SiAbsen</h1>
            <p>Sistem Absensi Mahasiswa</p>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>
            <button class="nav-item active" data-page="dashboard">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </button>
            <button class="nav-item" data-page="mahasiswa">
                <span class="nav-icon">👥</span>
                <span>Mahasiswa</span>
            </button>
            <button class="nav-item" data-page="matakuliah">
                <span class="nav-icon">📚</span>
                <span>Mata Kuliah</span>
            </button>

            <div class="nav-section-label">Absensi</div>
            <button class="nav-item" data-page="absensi">
                <span class="nav-icon">📋</span>
                <span>Sesi Absensi</span>
            </button>
            <button class="nav-item" data-page="scan">
                <span class="nav-icon">✅</span>
                <span>Absen Mandiri</span>
            </button>
            <button class="nav-item" data-page="rekap">
                <span class="nav-icon">📈</span>
                <span>Rekap Kehadiran</span>
            </button>
        </nav>

        <div class="sidebar-footer">
            <div>v1.0 &nbsp;·&nbsp; <?= date('Y') ?></div>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div class="sidebar-overlay"></div>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-content">

        <!-- Top Bar -->
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:14px">
                <button class="hamburger" aria-label="Menu">☰</button>
                <div class="topbar-title">
                    <h2 id="topbar-title">Dashboard</h2>
                    <p id="topbar-sub">Ringkasan sistem absensi</p>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date" id="live-date"></div>
            </div>
        </header>

        <!-- Pages -->
        <main class="page-content">

            <!-- ========================
                 PAGE: DASHBOARD
                 ======================== -->
            <div class="page active" id="page-dashboard">
                <div class="page-title">
                    <h2>Selamat Datang 👋</h2>
                    <p>Ringkasan data absensi mahasiswa hari ini</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card orange">
                        <div class="stat-icon">👥</div>
                        <div class="stat-value" id="stat-mahasiswa">—</div>
                        <div class="stat-label">Total Mahasiswa</div>
                    </div>
                    <div class="stat-card blue">
                        <div class="stat-icon">📚</div>
                        <div class="stat-value" id="stat-matakuliah">—</div>
                        <div class="stat-label">Mata Kuliah</div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-icon">📅</div>
                        <div class="stat-value" id="stat-sesi-aktif">—</div>
                        <div class="stat-label">Sesi Aktif</div>
                    </div>
                    <div class="stat-card red">
                        <div class="stat-icon">📈</div>
                        <div class="stat-value" id="stat-kehadiran">—</div>
                        <div class="stat-label">Rata-rata Kehadiran</div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="card">
                        <div class="card-header">
                            <h3>🕐 Sesi Absensi Aktif</h3>
                            <button class="btn btn-sm btn-primary" onclick="App.navigateTo('absensi')">Lihat Semua</button>
                        </div>
                        <div class="card-body" id="sesi-aktif-list">
                            <div class="spinner"></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>📋 Absensi Terbaru</h3>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Mata Kuliah</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-absensi-body">
                                    <tr><td colspan="5"><div class="spinner" style="margin:16px auto"></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================
                 PAGE: MAHASISWA
                 ======================== -->
            <div class="page" id="page-mahasiswa">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
                    <div class="page-title" style="margin:0">
                        <h2>Data</h2>
                        <p>Kelola data mahasiswa terdaftar</p>
                    </div>
                    <button class="btn btn-primary" onclick="Mahasiswa.openTambah()">＋ Tambah Mahasiswa</button>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Mahasiswa</h3>
                        <div class="search-bar">
                            <span class="search-icon">🔍</span>
                            <input type="text" class="form-control" placeholder="Cari nama / NIM..." oninput="Mahasiswa.filter(this.value)" style="width:220px">
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mahasiswa</th>
                                    <th>Jurusan</th>
                                    <th>Semester</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="mahasiswa-tbody">
                                <tr><td colspan="7"><div class="spinner" style="margin:20px auto"></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================
                 PAGE: MATA KULIAH
                 ======================== -->
            <div class="page" id="page-matakuliah">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
                    <div class="page-title" style="margin:0">
                        <h2>Mata Kuliah</h2>
                        <p>Kelola daftar mata kuliah</p>
                    </div>
                    <button class="btn btn-primary" onclick="MataKuliah.openTambah()">＋ Tambah MK</button>
                </div>

                <div class="card">
                    <div class="card-header"><h3>Daftar Mata Kuliah</h3></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Nama Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Semester</th>
                                    <th>Jurusan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="mk-tbody">
                                <tr><td colspan="7"><div class="spinner" style="margin:20px auto"></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================
                 PAGE: SESI ABSENSI
                 ======================== -->
            <div class="page" id="page-absensi">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
                    <div class="page-title" style="margin:0">
                        <h2>Sesi Absensi</h2>
                        <p>Buka dan kelola sesi kehadiran</p>
                    </div>
                    <button class="btn btn-primary" onclick="Absensi.openBuat()">＋ Buka Sesi Baru</button>
                </div>

                <div class="card">
                    <div class="card-header"><h3>Daftar Sesi Absensi</h3></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mata Kuliah</th>
                                    <th>Tanggal</th>
                                    <th>Pertemuan</th>
                                    <th>Kode Absen</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="sesi-tbody">
                                <tr><td colspan="7"><div class="spinner" style="margin:20px auto"></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================
                 PAGE: ABSEN MANDIRI
                 ======================== -->
            <div class="page" id="page-scan">
                <div class="page-title">
                    <h2>Absen Mandiri</h2>
                    <p>Mahasiswa masukkan NIM dan kode absensi yang diberikan dosen</p>
                </div>

                <div style="max-width:480px;margin:0 auto">
                    <div class="card">
                        <div class="card-header">
                            <h3>✅ Form Absensi</h3>
                        </div>
                        <div class="card-body">
                            <div style="text-align:center;margin-bottom:24px;padding:16px;background:var(--bg-secondary);border-radius:var(--radius);border:1px solid var(--border)">
                                <div style="font-size:2rem;margin-bottom:6px">📲</div>
                                <div style="font-size:0.85rem;color:var(--text-secondary)">Masukkan NIM dan kode absensi yang diberikan oleh dosen untuk mencatat kehadiran Anda</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">NIM Mahasiswa</label>
                                <input type="text" id="scan-nim" class="form-control" placeholder="Contoh: 2021001" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kode Absensi</label>
                                <input type="text" id="scan-kode" class="form-control" placeholder="Contoh: TI1234" style="text-transform:uppercase;letter-spacing:0.1em;font-size:1.1rem;font-weight:600" maxlength="8" autocomplete="off">
                            </div>
                            <button class="btn btn-primary" id="btn-scan" style="width:100%;justify-content:center;padding:12px" onclick="AbsensiMandiri.submit()">
                                ✓ Absen Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================
                 PAGE: REKAP
                 ======================== -->
            <div class="page" id="page-rekap">
                <div class="page-title">
                    <h2>Rekap Kehadiran</h2>
                    <p>Laporan rekapitulasi absensi mahasiswa</p>
                </div>

                <div class="card" style="margin-bottom:20px">
                    <div class="card-body">
                        <div style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:12px;align-items:end;flex-wrap:wrap">
                            <div class="form-group" style="margin:0">
                                <label class="form-label">Mata Kuliah</label>
                                <select id="rekap-mk" class="form-control">
                                    <option value="">-- Semua MK --</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin:0">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" id="rekap-dari" class="form-control">
                            </div>
                            <div class="form-group" style="margin:0">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" id="rekap-sampai" class="form-control">
                            </div>
                            <button class="btn btn-primary" onclick="Rekap.generate()">🔍 Tampilkan</button>
                        </div>
                    </div>
                </div>

                <div class="card" id="rekap-result" style="display:none">
                    <div class="card-header"><h3>Hasil Rekap</h3></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mahasiswa</th>
                                    <th style="text-align:center">Hadir</th>
                                    <th style="text-align:center">Izin</th>
                                    <th style="text-align:center">Sakit</th>
                                    <th style="text-align:center">Alpha</th>
                                    <th>% Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody id="rekap-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- ===== MODALS ===== -->

<!-- Modal Mahasiswa -->
<div class="modal-overlay" id="modal-mahasiswa">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-mhs-title">Tambah Mahasiswa</h3>
            <button class="modal-close" onclick="App.closeModal('modal-mahasiswa')">✕</button>
        </div>
        <div class="modal-body">
            <form id="form-mahasiswa" onsubmit="return false">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NIM <span style="color:var(--red)">*</span></label>
                        <input type="text" id="mhs-nim" class="form-control" placeholder="Nomor Induk Mahasiswa">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Semester</label>
                        <select id="mhs-semester" class="form-control">
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                            <option value="<?= $i ?>">Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span style="color:var(--red)">*</span></label>
                    <input type="text" id="mhs-nama" class="form-control" placeholder="Nama Lengkap Mahasiswa">
                </div>
                <div class="form-group">
                    <label class="form-label">Jurusan</label>
                    <select id="mhs-jurusan" class="form-control">
                        <option value="">-- Pilih Jurusan --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="mhs-email" class="form-control" placeholder="email@student.ac.id">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select id="mhs-status" class="form-control">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="App.closeModal('modal-mahasiswa')">Batal</button>
            <button class="btn btn-primary" onclick="Mahasiswa.simpan()">💾 Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Mata Kuliah -->
<div class="modal-overlay" id="modal-matakuliah">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-mk-title">Tambah Mata Kuliah</h3>
            <button class="modal-close" onclick="App.closeModal('modal-matakuliah')">✕</button>
        </div>
        <div class="modal-body">
            <form id="form-matakuliah" onsubmit="return false">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kode MK <span style="color:var(--red)">*</span></label>
                        <input type="text" id="mk-kode" class="form-control" placeholder="Contoh: TI101" style="text-transform:uppercase">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SKS</label>
                        <select id="mk-sks" class="form-control">
                            <option value="2">2 SKS</option>
                            <option value="3">3 SKS</option>
                            <option value="4">4 SKS</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Mata Kuliah <span style="color:var(--red)">*</span></label>
                    <input type="text" id="mk-nama" class="form-control" placeholder="Nama Mata Kuliah">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Jurusan</label>
                        <select id="mk-jurusan" class="form-control">
                            <option value="">-- Pilih Jurusan --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Semester</label>
                        <select id="mk-semester" class="form-control">
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                            <option value="<?= $i ?>">Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Dosen</label>
                    <input type="text" id="mk-dosen" class="form-control" placeholder="Nama Dosen Pengampu">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="App.closeModal('modal-matakuliah')">Batal</button>
            <button class="btn btn-primary" onclick="MataKuliah.simpan()">💾 Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Buat Sesi -->
<div class="modal-overlay" id="modal-sesi">
    <div class="modal">
        <div class="modal-header">
            <h3>Buka Sesi Absensi Baru</h3>
            <button class="modal-close" onclick="App.closeModal('modal-sesi')">✕</button>
        </div>
        <div class="modal-body">
            <form id="form-sesi" onsubmit="return false">
                <div class="form-group">
                    <label class="form-label">Mata Kuliah <span style="color:var(--red)">*</span></label>
                    <select id="sesi-mk" class="form-control">
                        <option value="">-- Pilih Mata Kuliah --</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="sesi-tanggal" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pertemuan Ke-</label>
                        <select id="sesi-pertemuan" class="form-control">
                            <?php for ($i = 1; $i <= 16; $i++): ?>
                            <option value="<?= $i ?>">Pertemuan <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div style="background:var(--bg-secondary);border:1px solid var(--border);border-radius:var(--radius);padding:12px;font-size:0.8rem;color:var(--text-muted)">
                    ℹ️ Kode absensi akan dibuat otomatis. Bagikan kode ini kepada mahasiswa.
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="App.closeModal('modal-sesi')">Batal</button>
            <button class="btn btn-primary" onclick="Absensi.buat()">🔓 Buka Sesi</button>
        </div>
    </div>
</div>

<!-- Modal Kode Absen -->
<div class="modal-overlay" id="modal-kode">
    <div class="modal">
        <div class="modal-header">
            <h3>🎉 Sesi Berhasil Dibuka!</h3>
            <button class="modal-close" onclick="App.closeModal('modal-kode')">✕</button>
        </div>
        <div class="modal-body">
            <p style="color:var(--text-secondary);font-size:0.875rem;margin-bottom:8px">Bagikan kode ini kepada mahasiswa:</p>
            <div class="code-display">
                <span class="code-value" id="kode-result">—</span>
                <span class="code-label">Kode Absensi</span>
            </div>
            <p style="color:var(--text-muted);font-size:0.78rem;text-align:center;margin-top:8px">Mahasiswa memasukkan kode ini di menu "Absen Mandiri"</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="App.closeModal('modal-kode')">Selesai</button>
        </div>
    </div>
</div>

<!-- Modal Detail Absensi -->
<div class="modal-overlay" id="modal-detail">
    <div class="modal" style="max-width:680px">
        <div class="modal-header">
            <div>
                <h3 id="detail-title">Detail Absensi</h3>
                <div style="font-size:0.78rem;color:var(--text-muted)" id="detail-info"></div>
            </div>
            <button class="modal-close" onclick="App.closeModal('modal-detail')">✕</button>
        </div>
        <div class="modal-body">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding:12px;background:var(--bg-secondary);border-radius:var(--radius)">
                <span style="font-size:0.82rem;color:var(--text-secondary)">Total hadir:</span>
                <span style="font-weight:700;color:var(--green)" id="detail-hadir">—</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mahasiswa</th>
                            <th>Waktu Absen</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="detail-tbody"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="App.closeModal('modal-detail')">Tutup</button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toast-container"></div>

<script src="assets/js/app.js"></script>
</body>
</html>
