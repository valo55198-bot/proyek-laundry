// Buka popup ketika tombol logout diklik
document.querySelectorAll('.logout-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const overlay = document.getElementById('logout-overlay');
        if(overlay) overlay.style.display = 'flex';
    });
});

// Tutup popup: tombol batal & tombol ×
document.querySelectorAll('#logout-overlay .btn-ghost, #logout-overlay .close').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('logout-overlay').style.display = 'none';
    });
});

// Tutup popup: klik di luar card
const logoutOverlay = document.getElementById('logout-overlay');
if (logoutOverlay) {
    logoutOverlay.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
}

// Konfirmasi logout: tombol Ya, Keluar
// URL logout dibaca dari data-logout-url agar berfungsi di root maupun di dalam /dashboard/
const confirmBtn = document.querySelector('#logout-overlay .btn-confirm');
if (confirmBtn) {
    confirmBtn.addEventListener('click', function() {
        const url = (logoutOverlay && logoutOverlay.dataset.logoutUrl)
            ? logoutOverlay.dataset.logoutUrl
            : 'logout.php';
        window.location.href = url;
    });
}
