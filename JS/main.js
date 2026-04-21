// ==========================================
// ===== LOGIKA LANDING PAGE (index.html) ===
// ==========================================

const toggleBtn = document.getElementById('toggle-btn');
const sidebar   = document.getElementById('sidebar');
const overlay   = document.getElementById('overlay');

// Pastikan elemen ada sebelum menjalankan event listener
if (toggleBtn && sidebar && overlay) {
const openSidebar  = () => { sidebar.classList.add('open'); toggleBtn.classList.add('open'); overlay.classList.add('active'); };
const closeSidebar = () => { sidebar.classList.remove('open'); toggleBtn.classList.remove('open'); overlay.classList.remove('active'); };

toggleBtn.addEventListener('click', () => sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
overlay.addEventListener('click', closeSidebar);
}

// Navigasi Landing Page
document.querySelectorAll('.nav-link').forEach(link => {
link.addEventListener('click', e => {
    e.preventDefault();
    const target = link.dataset.section;
    if (!target) return;
    document.querySelectorAll('#sidebar .nav-link').forEach(l => l.classList.remove('active'));
    document.querySelectorAll(`#sidebar [data-section="${target}"]`).forEach(l => l.classList.add('active'));
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    
    const sec = document.getElementById(target);
    if (sec) { 
    sec.classList.add('active'); 
    window.scrollTo({ top: 0, behavior: 'smooth' }); 
    }
    
    // Tutup sidebar jika di mobile
    if (sidebar && sidebar.classList.contains('open')) {
    sidebar.classList.remove('open'); 
    toggleBtn.classList.remove('open'); 
    overlay.classList.remove('active');
    }
});
});

// Form Feedback Landing Page
const fbForm = document.getElementById('feedbackForm');
if (fbForm) {
fbForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('fbSubmitBtn');
    btn.textContent = '✅ Terkirim!';
    btn.style.background = 'linear-gradient(90deg,#10b981,#34d399)';
    setTimeout(() => { btn.textContent = 'Kirim Feedback'; btn.style.background = ''; this.reset(); }, 3000);
});
}

// Form Contact Landing Page
const ctForm = document.getElementById('contactForm');
if (ctForm) {
ctForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('ctSubmitBtn');
    btn.textContent = '✅ Terkirim!';
    btn.style.background = 'linear-gradient(90deg,#10b981,#34d399)';
    setTimeout(() => { btn.textContent = 'Kirim Pesan'; btn.style.background = ''; this.reset(); }, 3000);
});
}


// ==========================================
// ===== LOGIKA DASHBOARD (dashboard.html) ==
// ==========================================

// Set tanggal di Topbar Dashboard
const pageDateEl = document.getElementById('pageDate');
if (pageDateEl) {
const now = new Date();
pageDateEl.textContent = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
}

// Fungsi Tab Switching (Dibuat global dengan window agar bisa dipanggil lewat onclick di HTML)
window.switchTab = function(tab) {
document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
const targetTab = document.getElementById('tab-' + tab);
if (targetTab) targetTab.classList.remove('hidden');

document.querySelectorAll('.sidebar-link').forEach(link => {
    link.classList.remove('active');
    if (link.dataset.tab === tab) link.classList.add('active');
});

const titles = { beranda:'Dashboard', jadwal:'Jadwal Vaksin', riwayat:'Riwayat Imunisasi', notifikasi:'Notifikasi', konsultasi:'Konsultasi Dokter', profil:'Profil Saya' };
const pageTitleEl = document.getElementById('pageTitle');
if (pageTitleEl) pageTitleEl.textContent = titles[tab] || 'Dashboard';

const profileMenuEl = document.getElementById('profileMenu');
if (profileMenuEl) profileMenuEl.classList.remove('open');
}

// Event listener untuk menu sidebar Dashboard
document.querySelectorAll('.sidebar-link').forEach(link => {
link.addEventListener('click', e => { 
    e.preventDefault(); 
    window.switchTab(link.dataset.tab); 
});
});

// Dropdown Profil
const profileBtn  = document.getElementById('profileBtn');
const profileMenu = document.getElementById('profileMenu');
if (profileBtn && profileMenu) {
profileBtn.addEventListener('click', e => { 
    e.stopPropagation(); 
    profileMenu.classList.toggle('open'); 
});
document.addEventListener('click', () => profileMenu.classList.remove('open'));
}

// Animasi Progress Bar Imunisasi
const progressBars = document.querySelectorAll('.progress-bar');
if (progressBars.length > 0) {
setTimeout(() => {
    progressBars.forEach(bar => {
    bar.style.width = bar.dataset.width;
    });
}, 400);
}