// ============================================
// SISTEM ABSENSI MAHASISWA - Main JavaScript
// ============================================

const App = {
    currentPage: 'dashboard',

    // ---- Navigasi ----
    navigateTo(page) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));

        const pageEl = document.getElementById('page-' + page);
        if (pageEl) pageEl.classList.add('active');

        const navEl = document.querySelector(`[data-page="${page}"]`);
        if (navEl) navEl.classList.add('active');

        this.currentPage = page;

        // Update topbar title
        const titles = {
            dashboard: { title: 'Dashboard', sub: 'Ringkasan sistem absensi' },
            mahasiswa: { title: 'Data Mahasiswa', sub: 'Kelola data mahasiswa' },
            matakuliah: { title: 'Mata Kuliah', sub: 'Kelola mata kuliah' },
            absensi: { title: 'Sesi Absensi', sub: 'Buka dan kelola sesi absensi' },
            rekap: { title: 'Rekap Absensi', sub: 'Laporan kehadiran mahasiswa' },
        };

        if (titles[page]) {
            document.getElementById('topbar-title').textContent = titles[page].title;
            document.getElementById('topbar-sub').textContent = titles[page].sub;
        }

        // Load data for page
        this.loadPage(page);

        // Close sidebar on mobile
        if (window.innerWidth <= 900) this.closeSidebar();
    },

    loadPage(page) {
        switch (page) {
            case 'dashboard': Dashboard.load(); break;
            case 'mahasiswa': Mahasiswa.load(); break;
            case 'matakuliah': MataKuliah.load(); break;
            case 'absensi': Absensi.load(); break;
            case 'rekap': Rekap.load(); break;
        }
    },

    // ---- Sidebar ----
    openSidebar() {
        document.querySelector('.sidebar').classList.add('open');
        document.querySelector('.sidebar-overlay').classList.add('active');
    },
    closeSidebar() {
        document.querySelector('.sidebar').classList.remove('open');
        document.querySelector('.sidebar-overlay').classList.remove('active');
    },

    // ---- Toast ----
    toast(message, type = 'info') {
        const icons = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };
        const container = document.getElementById('toast-container');
        const el = document.createElement('div');
        el.className = `toast ${type}`;
        el.innerHTML = `<span>${icons[type] || 'ℹ'}</span><span>${message}</span>`;
        container.appendChild(el);
        setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateX(20px)'; el.style.transition = '0.3s'; setTimeout(() => el.remove(), 300); }, 3500);
    },

    // ---- Modal ----
    openModal(id) { document.getElementById(id).classList.add('active'); },
    closeModal(id) { document.getElementById(id).classList.remove('active'); },

    // ---- API Call ----
    async api(endpoint, method = 'GET', data = null) {
        const options = { method, headers: { 'Content-Type': 'application/json' } };
        if (data) options.body = JSON.stringify(data);
        const response = await fetch('api/' + endpoint, options);
        return response.json();
    },

    // ---- Format tanggal ----
    formatDate(dateStr) {
        const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
        const d = new Date(dateStr);
        return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
    },

    // ---- Live clock ----
    startClock() {
        const update = () => {
            const now = new Date();
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
            const el = document.getElementById('live-date');
            if (el) el.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} — ${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
        };
        update();
        setInterval(update, 1000);
    },

    // ---- Inisial avatar ----
    getInitials(name) {
        return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
    },
};

// ============================================
// DASHBOARD
// ============================================
const Dashboard = {
    async load() {
        const res = await App.api('dashboard.php');
        if (!res.success) return;
        const d = res.data;
        document.getElementById('stat-mahasiswa').textContent = d.total_mahasiswa || 0;
        document.getElementById('stat-matakuliah').textContent = d.total_mk || 0;
        document.getElementById('stat-sesi-aktif').textContent = d.sesi_aktif || 0;
        document.getElementById('stat-kehadiran').textContent = (d.pct_kehadiran || 0) + '%';
        this.renderRecentAbsensi(d.recent_absensi || []);
        this.renderSesiAktif(d.sesi_list || []);
    },

    renderRecentAbsensi(data) {
        const tbody = document.getElementById('recent-absensi-body');
        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><div class="empty-icon">📋</div><div class="empty-title">Belum ada data absensi</div></div></td></tr>`;
            return;
        }
        tbody.innerHTML = data.map(row => `
            <tr>
                <td><div class="td-primary">${row.nama}</div><div style="font-size:0.72rem;color:var(--text-muted)">${row.nim}</div></td>
                <td>${row.nama_mk}</td>
                <td>${App.formatDate(row.tanggal)}</td>
                <td>${row.waktu ? row.waktu.slice(11,16) : '-'}</td>
                <td><span class="badge badge-${row.status}">${row.status}</span></td>
            </tr>`).join('');
    },

    renderSesiAktif(data) {
        const el = document.getElementById('sesi-aktif-list');
        if (!data.length) {
            el.innerHTML = `<div class="empty-state"><div class="empty-icon">🎯</div><div class="empty-title">Tidak ada sesi aktif</div></div>`;
            return;
        }
        el.innerHTML = data.map(s => `
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border)">
                <div>
                    <div style="font-weight:500;color:var(--text-primary);font-size:0.875rem">${s.nama_mk}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)">Pertemuan ${s.pertemuan_ke} · ${App.formatDate(s.tanggal)}</div>
                </div>
                <div style="text-align:right">
                    <span class="badge badge-buka">🟢 ${s.kode_absen}</span>
                </div>
            </div>`).join('');
    }
};

// ============================================
// MAHASISWA
// ============================================
const Mahasiswa = {
    data: [],
    editId: null,

    async load() {
        const res = await App.api('mahasiswa.php');
        if (res.success) { this.data = res.data; this.render(this.data); }
    },

    render(data) {
        const tbody = document.getElementById('mahasiswa-tbody');
        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><div class="empty-icon">👤</div><div class="empty-title">Tidak ada data mahasiswa</div></div></td></tr>`;
            return;
        }
        tbody.innerHTML = data.map((m, i) => `
            <tr>
                <td style="color:var(--text-muted);font-size:0.78rem">${i + 1}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="avatar" style="background:${this.avatarColor(m.nama)}">${App.getInitials(m.nama)}</div>
                        <div>
                            <div class="td-primary">${m.nama}</div>
                            <div style="font-size:0.72rem;color:var(--text-muted)">${m.nim}</div>
                        </div>
                    </div>
                </td>
                <td>${m.nama_jurusan || '-'}</td>
                <td style="text-align:center">Sem ${m.semester}</td>
                <td style="font-size:0.8rem;color:var(--text-muted)">${m.email || '-'}</td>
                <td><span class="badge badge-${m.status}">${m.status}</span></td>
                <td>
                    <div style="display:flex;gap:6px">
                        <button class="btn btn-sm btn-secondary btn-icon" onclick="Mahasiswa.openEdit(${m.id})" title="Edit">✏️</button>
                        <button class="btn btn-sm btn-danger btn-icon" onclick="Mahasiswa.hapus(${m.id}, '${m.nama}')" title="Hapus">🗑️</button>
                    </div>
                </td>
            </tr>`).join('');
    },

    avatarColor(name) {
        const colors = ['linear-gradient(135deg,#f0883e,#ff6b35)', 'linear-gradient(135deg,#58a6ff,#1f6feb)', 'linear-gradient(135deg,#3fb950,#2ea043)', 'linear-gradient(135deg,#bc8cff,#8b5cf6)', 'linear-gradient(135deg,#e3b341,#d29922)'];
        return colors[name.charCodeAt(0) % colors.length];
    },

    filter(q) {
        const filtered = this.data.filter(m => m.nama.toLowerCase().includes(q.toLowerCase()) || m.nim.includes(q));
        this.render(filtered);
    },

    async loadJurusan(selectId) {
        const res = await App.api('jurusan.php');
        if (!res.success) return;
        const sel = document.getElementById(selectId);
        sel.innerHTML = '<option value="">-- Pilih Jurusan --</option>' + res.data.map(j => `<option value="${j.id}">${j.nama_jurusan}</option>`).join('');
    },

    openTambah() {
        this.editId = null;
        document.getElementById('modal-mhs-title').textContent = 'Tambah Mahasiswa';
        document.getElementById('form-mahasiswa').reset();
        this.loadJurusan('mhs-jurusan');
        App.openModal('modal-mahasiswa');
    },

    openEdit(id) {
        this.editId = id;
        const m = this.data.find(x => x.id === id);
        if (!m) return;
        document.getElementById('modal-mhs-title').textContent = 'Edit Mahasiswa';
        this.loadJurusan('mhs-jurusan').then(() => {
            document.getElementById('mhs-jurusan').value = m.jurusan_id;
        });
        document.getElementById('mhs-nim').value = m.nim;
        document.getElementById('mhs-nama').value = m.nama;
        document.getElementById('mhs-semester').value = m.semester;
        document.getElementById('mhs-email').value = m.email || '';
        document.getElementById('mhs-status').value = m.status;
        App.openModal('modal-mahasiswa');
    },

    async simpan() {
        const body = {
            nim: document.getElementById('mhs-nim').value,
            nama: document.getElementById('mhs-nama').value,
            jurusan_id: document.getElementById('mhs-jurusan').value,
            semester: document.getElementById('mhs-semester').value,
            email: document.getElementById('mhs-email').value,
            status: document.getElementById('mhs-status').value,
        };
        if (!body.nim || !body.nama) { App.toast('NIM dan Nama wajib diisi!', 'warning'); return; }

        const endpoint = this.editId ? `mahasiswa.php?id=${this.editId}` : 'mahasiswa.php';
        const method = this.editId ? 'PUT' : 'POST';
        const res = await App.api(endpoint, method, body);
        if (res.success) {
            App.toast(res.message, 'success');
            App.closeModal('modal-mahasiswa');
            this.load();
        } else {
            App.toast(res.message, 'error');
        }
    },

    async hapus(id, nama) {
        if (!confirm(`Hapus mahasiswa "${nama}"?`)) return;
        const res = await App.api(`mahasiswa.php?id=${id}`, 'DELETE');
        if (res.success) { App.toast(res.message, 'success'); this.load(); }
        else App.toast(res.message, 'error');
    },
};

// ============================================
// MATA KULIAH
// ============================================
const MataKuliah = {
    data: [],
    editId: null,

    async load() {
        const res = await App.api('matakuliah.php');
        if (res.success) { this.data = res.data; this.render(this.data); }
    },

    render(data) {
        const tbody = document.getElementById('mk-tbody');
        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📚</div><div class="empty-title">Tidak ada mata kuliah</div></div></td></tr>`;
            return;
        }
        tbody.innerHTML = data.map((m, i) => `
            <tr>
                <td style="color:var(--text-muted);font-size:0.78rem">${i + 1}</td>
                <td><span style="font-family:monospace;background:var(--bg-hover);padding:2px 8px;border-radius:6px;font-size:0.8rem;color:var(--accent)">${m.kode_mk}</span></td>
                <td class="td-primary">${m.nama_mk}</td>
                <td style="text-align:center">${m.sks} SKS</td>
                <td style="text-align:center">Sem ${m.semester}</td>
                <td>${m.nama_jurusan || '-'}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <button class="btn btn-sm btn-secondary btn-icon" onclick="MataKuliah.openEdit(${m.id})">✏️</button>
                        <button class="btn btn-sm btn-danger btn-icon" onclick="MataKuliah.hapus(${m.id}, '${m.nama_mk}')">🗑️</button>
                    </div>
                </td>
            </tr>`).join('');
    },

    async loadJurusan() {
        const res = await App.api('jurusan.php');
        if (!res.success) return;
        const sel = document.getElementById('mk-jurusan');
        sel.innerHTML = '<option value="">-- Pilih Jurusan --</option>' + res.data.map(j => `<option value="${j.id}">${j.nama_jurusan}</option>`).join('');
    },

    openTambah() {
        this.editId = null;
        document.getElementById('modal-mk-title').textContent = 'Tambah Mata Kuliah';
        document.getElementById('form-matakuliah').reset();
        this.loadJurusan();
        App.openModal('modal-matakuliah');
    },

    openEdit(id) {
        this.editId = id;
        const m = this.data.find(x => x.id === id);
        if (!m) return;
        document.getElementById('modal-mk-title').textContent = 'Edit Mata Kuliah';
        this.loadJurusan().then(() => {
            document.getElementById('mk-jurusan').value = m.jurusan_id;
        });
        document.getElementById('mk-kode').value = m.kode_mk;
        document.getElementById('mk-nama').value = m.nama_mk;
        document.getElementById('mk-sks').value = m.sks;
        document.getElementById('mk-semester').value = m.semester;
        document.getElementById('mk-dosen').value = m.dosen || '';
        App.openModal('modal-matakuliah');
    },

    async simpan() {
        const body = {
            kode_mk: document.getElementById('mk-kode').value,
            nama_mk: document.getElementById('mk-nama').value,
            sks: document.getElementById('mk-sks').value,
            semester: document.getElementById('mk-semester').value,
            jurusan_id: document.getElementById('mk-jurusan').value,
            dosen: document.getElementById('mk-dosen').value,
        };
        if (!body.kode_mk || !body.nama_mk) { App.toast('Kode dan Nama MK wajib diisi!', 'warning'); return; }
        const endpoint = this.editId ? `matakuliah.php?id=${this.editId}` : 'matakuliah.php';
        const method = this.editId ? 'PUT' : 'POST';
        const res = await App.api(endpoint, method, body);
        if (res.success) { App.toast(res.message, 'success'); App.closeModal('modal-matakuliah'); this.load(); }
        else App.toast(res.message, 'error');
    },

    async hapus(id, nama) {
        if (!confirm(`Hapus mata kuliah "${nama}"?`)) return;
        const res = await App.api(`matakuliah.php?id=${id}`, 'DELETE');
        if (res.success) { App.toast(res.message, 'success'); this.load(); }
        else App.toast(res.message, 'error');
    },
};

// ============================================
// ABSENSI (SESI)
// ============================================
const Absensi = {
    data: [],
    activeSesiId: null,
    attendanceData: [],

    async load() {
        const res = await App.api('sesi.php');
        if (res.success) { this.data = res.data; this.render(); }
    },

    render() {
        const tbody = document.getElementById('sesi-tbody');
        if (!this.data.length) {
            tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📅</div><div class="empty-title">Belum ada sesi absensi</div></div></td></tr>`;
            return;
        }
        tbody.innerHTML = this.data.map((s, i) => `
            <tr>
                <td style="color:var(--text-muted);font-size:0.78rem">${i + 1}</td>
                <td class="td-primary">${s.nama_mk}</td>
                <td>${App.formatDate(s.tanggal)}</td>
                <td style="text-align:center">Ke-${s.pertemuan_ke}</td>
                <td><div class="code-display" style="padding:8px 16px;margin:0;display:inline-block"><span class="code-value" style="font-size:1.3rem;letter-spacing:0.15em">${s.kode_absen}</span></div></td>
                <td><span class="badge badge-${s.status === 'aktif' ? 'buka' : 'tutup'}">${s.status === 'aktif' ? '🟢 Aktif' : '🔴 Tutup'}</span></td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <button class="btn btn-sm btn-secondary" onclick="Absensi.lihatDetail(${s.id})">👁 Detail</button>
                        ${s.status === 'aktif' ? `<button class="btn btn-sm btn-danger" onclick="Absensi.tutupSesi(${s.id})">🔒 Tutup</button>` : ''}
                    </div>
                </td>
            </tr>`).join('');
    },

    async loadMK() {
        const res = await App.api('matakuliah.php');
        if (!res.success) return;
        const sel = document.getElementById('sesi-mk');
        sel.innerHTML = '<option value="">-- Pilih Mata Kuliah --</option>' + res.data.map(m => `<option value="${m.id}">${m.kode_mk} - ${m.nama_mk}</option>`).join('');
    },

    openBuat() {
        document.getElementById('form-sesi').reset();
        document.getElementById('sesi-tanggal').value = new Date().toISOString().split('T')[0];
        this.loadMK();
        App.openModal('modal-sesi');
    },

    async buat() {
        const body = {
            mk_id: document.getElementById('sesi-mk').value,
            tanggal: document.getElementById('sesi-tanggal').value,
            pertemuan_ke: document.getElementById('sesi-pertemuan').value,
        };
        if (!body.mk_id) { App.toast('Pilih mata kuliah!', 'warning'); return; }
        const res = await App.api('sesi.php', 'POST', body);
        if (res.success) {
            App.toast(res.message, 'success');
            App.closeModal('modal-sesi');
            this.load();
            // Show the code
            const kode = res.data?.kode_absen;
            if (kode) {
                document.getElementById('kode-result').textContent = kode;
                App.openModal('modal-kode');
            }
        } else {
            App.toast(res.message, 'error');
        }
    },

    async lihatDetail(sesiId) {
        this.activeSesiId = sesiId;
        const res = await App.api(`absensi.php?sesi_id=${sesiId}`);
        if (!res.success) { App.toast('Gagal memuat data', 'error'); return; }
        this.attendanceData = res.data;
        const sesi = this.data.find(s => s.id === sesiId);
        document.getElementById('detail-title').textContent = `Detail: ${sesi?.nama_mk || ''}`;
        document.getElementById('detail-info').textContent = `${sesi ? App.formatDate(sesi.tanggal) + ' · Pertemuan ke-' + sesi.pertemuan_ke : ''}`;
        this.renderDetail(res.data);
        App.openModal('modal-detail');
    },

    renderDetail(data) {
        const hadir = data.filter(d => d.status === 'hadir').length;
        const total = data.length;
        document.getElementById('detail-hadir').textContent = `${hadir}/${total}`;

        const tbody = document.getElementById('detail-tbody');
        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state"><div class="empty-icon">📋</div><div class="empty-title">Belum ada yang absen</div></div></td></tr>`;
            return;
        }
        tbody.innerHTML = data.map((d, i) => `
            <tr>
                <td style="color:var(--text-muted)">${i + 1}</td>
                <td>
                    <div class="td-primary">${d.nama}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)">${d.nim}</div>
                </td>
                <td>${d.waktu_absen ? d.waktu_absen.slice(11, 16) : '-'}</td>
                <td>
                    <select class="form-control" style="padding:4px 8px;font-size:0.8rem;width:auto" onchange="Absensi.updateStatus(${d.absensi_id}, this.value)">
                        ${['hadir','izin','sakit','alpha'].map(s => `<option value="${s}" ${d.status === s ? 'selected' : ''}>${s.charAt(0).toUpperCase() + s.slice(1)}</option>`).join('')}
                    </select>
                </td>
            </tr>`).join('');
    },

    async updateStatus(absensiId, status) {
        const res = await App.api(`absensi.php?id=${absensiId}`, 'PUT', { status });
        if (res.success) App.toast('Status diperbarui', 'success');
        else App.toast('Gagal memperbarui', 'error');
    },

    async tutupSesi(id) {
        if (!confirm('Tutup sesi absensi ini?')) return;
        const res = await App.api(`sesi.php?id=${id}`, 'PUT', { status: 'tutup' });
        if (res.success) { App.toast('Sesi ditutup', 'success'); this.load(); }
        else App.toast(res.message, 'error');
    },
};

// ============================================
// REKAP
// ============================================
const Rekap = {
    async load() {
        const res = await App.api('matakuliah.php');
        if (!res.success) return;
        const sel = document.getElementById('rekap-mk');
        sel.innerHTML = '<option value="">-- Semua MK --</option>' + res.data.map(m => `<option value="${m.id}">${m.kode_mk} - ${m.nama_mk}</option>`).join('');
    },

    async generate() {
        const mk_id = document.getElementById('rekap-mk').value;
        const tanggal_dari = document.getElementById('rekap-dari').value;
        const tanggal_sampai = document.getElementById('rekap-sampai').value;

        let q = 'rekap.php?';
        if (mk_id) q += `mk_id=${mk_id}&`;
        if (tanggal_dari) q += `dari=${tanggal_dari}&`;
        if (tanggal_sampai) q += `sampai=${tanggal_sampai}&`;

        const res = await App.api(q);
        if (!res.success) { App.toast(res.message || 'Gagal memuat rekap', 'error'); return; }
        this.render(res.data);
    },

    render(data) {
        document.getElementById('rekap-result').style.display = 'block';
        const tbody = document.getElementById('rekap-tbody');
        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📊</div><div class="empty-title">Tidak ada data</div></div></td></tr>`;
            return;
        }
        tbody.innerHTML = data.map((r, i) => {
            const total = r.hadir + r.izin + r.sakit + r.alpha;
            const pct = total > 0 ? Math.round((r.hadir / total) * 100) : 0;
            const barClass = pct >= 75 ? 'green' : pct >= 50 ? 'orange' : 'red';
            return `
            <tr>
                <td style="color:var(--text-muted)">${i + 1}</td>
                <td>
                    <div class="td-primary">${r.nama}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted)">${r.nim}</div>
                </td>
                <td style="text-align:center;color:var(--green)">${r.hadir}</td>
                <td style="text-align:center;color:var(--yellow)">${r.izin}</td>
                <td style="text-align:center;color:var(--blue)">${r.sakit}</td>
                <td style="text-align:center;color:var(--red)">${r.alpha}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px">
                        <div class="progress-bar" style="flex:1">
                            <div class="progress-fill ${barClass}" style="width:${pct}%"></div>
                        </div>
                        <span style="font-size:0.8rem;font-weight:600;color:${pct >= 75 ? 'var(--green)' : pct >= 50 ? 'var(--accent)' : 'var(--red)'};min-width:38px;text-align:right">${pct}%</span>
                    </div>
                </td>
            </tr>`;
        }).join('');
    },
};

// ============================================
// ABSENSI MANDIRI (MAHASISWA SCAN KODE)
// ============================================
const AbsensiMandiri = {
    async submit() {
        const nim = document.getElementById('scan-nim').value.trim();
        const kode = document.getElementById('scan-kode').value.trim().toUpperCase();
        if (!nim || !kode) { App.toast('NIM dan kode harus diisi!', 'warning'); return; }

        const btn = document.getElementById('btn-scan');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Memproses...';

        const res = await App.api('scan.php', 'POST', { nim, kode });
        btn.disabled = false;
        btn.innerHTML = '✓ Absen Sekarang';

        if (res.success) {
            App.toast(res.message, 'success');
            document.getElementById('scan-nim').value = '';
            document.getElementById('scan-kode').value = '';
            document.getElementById('scan-nim').focus();
        } else {
            App.toast(res.message, 'error');
        }
    },
};

// ============================================
// INIT
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    App.startClock();
    App.navigateTo('dashboard');

    // Nav clicks
    document.querySelectorAll('.nav-item[data-page]').forEach(el => {
        el.addEventListener('click', () => App.navigateTo(el.dataset.page));
    });

    // Hamburger
    document.querySelector('.hamburger')?.addEventListener('click', App.openSidebar.bind(App));
    document.querySelector('.sidebar-overlay')?.addEventListener('click', App.closeSidebar.bind(App));

    // Keyboard: Enter on scan fields
    ['scan-nim', 'scan-kode'].forEach(id => {
        document.getElementById(id)?.addEventListener('keypress', e => { if (e.key === 'Enter') AbsensiMandiri.submit(); });
    });
});
