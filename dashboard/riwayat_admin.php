<?php
/**
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 *  RUMAH LAUNDRY â€” Riwayat Transaksi (Admin)
 *  Menampilkan pesanan Selesai dan Dibatalkan
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 */
require_once 'config.php';
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$flash = getFlash();
$adminNama = e($_SESSION['nama'] ?? 'Admin');

$filterStatus = $_GET['filter_status'] ?? '';
if ($filterStatus === 'Selesai') {
    $data = $conn->query("SELECT * FROM transaksi WHERE status = 'Selesai' ORDER BY id_laundry DESC");
} elseif ($filterStatus === 'Dibatalkan') {
    $data = $conn->query("SELECT * FROM transaksi WHERE status = 'Dibatalkan' ORDER BY id_laundry DESC");
} else {
    $data = $conn->query("SELECT * FROM transaksi WHERE status IN ('Selesai','Dibatalkan') ORDER BY id_laundry DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi â€” Rumah Laundry Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/dashboard-modals.css?v=<?= time() ?>">
</head>
<body>

    <!-- â•â•â•â•â•â•â•â•â•â•â• SIDEBAR â•â•â•â•â•â•â•â•â•â•â• -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar__header">
            <div class="sidebar__logo">
                <div class="sidebar__logo-icon"><img src="../assets/img/RL.png" alt="Logo" style="width:40px;height:40px;background-color:#1F2937;padding:4px;border-radius:8px;"></div>
                <div class="sidebar__logo-text"><span>Rumah Laundry</span><small>Admin Panel</small></div>
            </div>
            <button class="sidebar__close" id="sidebarClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="sidebar__nav">
            <span class="sidebar__label">MENU UTAMA</span>
            <ul class="sidebar__menu">
                <li><a href="dashboard.php" class="sidebar__link"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
                <li><a href="table-op.php" class="sidebar__link"><i class="fas fa-receipt"></i><span>Transaksi</span></a></li>
                <li><a href="riwayat_admin.php" class="sidebar__link active"><i class="fas fa-clock-rotate-left"></i><span>Riwayat Transaksi</span></a></li>
                <li><a href="pesan.php" class="sidebar__link"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li>
            </ul>
            <span class="sidebar__label">LAINNYA</span>
            <ul class="sidebar__menu">
                <li><a href="../index.php" class="sidebar__link"><i class="fas fa-globe"></i><span>Lihat Website</span></a></li>
                <li><a href="logout.php" class="sidebar__link sidebar__link--logout" onclick="return confirm('Anda yakin ingin keluar?')"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <div class="overlay" id="overlay"></div>

    <div class="main-wrapper" id="mainWrapper">
        <header class="topbar" id="topbar">
            <div class="topbar__left">
                <button class="topbar__toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar__title">
                    <h1>Riwayat Transaksi</h1>
                    <p>Pesanan selesai dan dibatalkan</p>
                </div>
            </div>
            <div class="topbar__right">
                <div class="topbar__profile" id="profileToggle">
                    <div class="topbar__avatar"><?= strtoupper(substr($adminNama, 0, 1)) ?></div>
                    <span class="topbar__name"><?= $adminNama ?></span>
                    <i class="fas fa-chevron-down"></i>
                    <div class="topbar__dropdown">
                        <a href="../index.php"><i class="fas fa-globe"></i> Lihat Website</a>
                        <div class="topbar__dropdown-divider"></div>
                        <a href="logout.php" class="topbar__dropdown--danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">

            <?php if ($flash): ?>
            <div style="padding:14px 18px;border-radius:10px;margin-bottom:20px;background:<?= $flash['type']=='success'?'#d1fae5':'#fee2e2' ?>;color:<?= $flash['type']=='success'?'#065f46':'#991b1b' ?>;font-weight:500;">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <div class="table-section" data-animate="fade-up">
                <div class="table-section__header">
                    <div>
                        <h2 class="table-section__title">Riwayat Pesanan</h2>
                        <p class="table-section__subtitle">Total <?= $data->num_rows ?> data</p>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                        <!-- Tombol Pilih -->
                        <button id="toggleSelectBtn" onclick="toggleSelectMode()"
                            style="padding:10px 14px;border:1px solid transparent;border-radius:50px;
                                   font-family:'Inter',sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;
                                   background:var(--bg);color:var(--text);transition:.35s cubic-bezier(.22,1,.36,1);"
                            onmouseover="if(!this.classList.contains('active')) this.style.borderColor='var(--border)'"
                            onmouseout="if(!this.classList.contains('active')) this.style.borderColor='transparent'">
                            <i class="fas fa-check-square"></i> Pilih
                        </button>
                        <!-- Filter Status -->
                        <form method="GET" action="riwayat_admin.php" style="display:flex;gap:8px;align-items:center;">
                            <select name="filter_status" onchange="this.form.submit()"
                                style="padding:8px 14px;border:2px solid var(--border);border-radius:8px;font-family:'Inter',sans-serif;font-size:.85rem;outline:none;background:#fff;cursor:pointer;">
                                <option value="">Semua Riwayat</option>
                                <option value="Selesai" <?= $filterStatus==='Selesai'?'selected':'' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= $filterStatus==='Dibatalkan'?'selected':'' ?>>Dibatalkan</option>
                            </select>
                            <?php if (!empty($filterStatus)): ?>
                                <a href="riwayat_admin.php" style="font-size:.8rem;color:var(--text-muted);">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Bulk Toolbar -->
                <div id="bulkToolbar" style="
                    display: none;
                    background: #1F2937;
                    border-radius: 12px;
                    padding: 10px 20px;
                    margin: 0 16px 12px;
                    color: #fff;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    width: calc(100% - 32px);">
                    <span><strong id="selectedCount">0</strong> data dipilih</span>
                    <div style="display:flex;gap:8px;">
                        <form method="POST" action="config.php" id="bulkForm">
                            <input type="hidden" name="bulkHapusRiwayat" value="1">
                            <div id="bulkInputs"></div>
                            <button type="button" onclick="confirmBulkDelete()"
                                style="background:#fff;color:#111827;border:none;padding:8px 16px;
                                       border-radius:8px;font-weight:700;cursor:pointer;font-size:.85rem;
                                       display:inline-flex;align-items:center;gap:6px;">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                        </form>
                        <button onclick="clearSelection()"
                            style="background:rgba(255,255,255,.15);color:#fff;border:none;padding:8px 14px;
                                   border-radius:8px;cursor:pointer;font-size:.85rem;">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table" id="riwayatTable">
                        <thead>
                            <tr style="background-color: #1F2937;">
                                <th class="cb-col" style="width:35px;text-align:center;">
                                    <input type="checkbox" id="selectAll" title="Pilih Semua"
                                        style="width:16px;height:16px;cursor:pointer;accent-color:#fff;">
                                </th>
                                <th style="background-color:#1F2937;color:#fff;">No</th>
                                <th style="background-color:#1F2937;color:#fff;">Kode Order</th>
                                <th style="background-color:#1F2937;color:#fff;">Nama</th>
                                <th style="background-color:#1F2937;color:#fff;">No HP</th>
                                <th style="background-color:#1F2937;color:#fff;">Jenis Pencucian</th>
                                <th style="background-color:#1F2937;color:#fff;">Jenis Layanan</th>
                                <th style="background-color:#1F2937;color:#fff;">Tgl Jemput</th>
                                <th style="background-color:#1F2937;color:#fff;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($data->num_rows > 0): ?>
                                <?php $no = 1; while($row = $data->fetch_assoc()): ?>
                                    <tr>
                                        <td class="cb-col" style="text-align:center;">
                                            <input type="checkbox" class="row-cb" value="<?= $row['id_laundry'] ?>"
                                                style="width:16px;height:16px;cursor:pointer;accent-color:#1F2937;">
                                        </td>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <code style="background:var(--bg);padding:3px 8px;border-radius:4px;font-size:.8rem;font-weight:600;">
                                                <?= e($row['kode_order']) ?>
                                            </code>
                                        </td>
                                        <td><strong><?= e($row['nama']) ?></strong></td>
                                        <td><?= e($row['no_hp']) ?></td>
                                        <td><?= e($row['jenis_pencucian']) ?></td>
                                        <td><?= e($row['jenis_layanan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_penjemputan'])) ?></td>
                                        <td>
                                            <?php
                                                $s = $row['status'] ?? 'Selesai';
                                                $cls = 'badge--done';
                                                if ($s == 'Dibatalkan') $cls = 'badge--cancelled';
                                            ?>
                                            <span class="badge <?= $cls ?>"><span class="badge__dot"></span><?= $s ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted);">
                                        <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                                        Belum ada riwayat transaksi
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script src="../script/dashboard.js"></script>
    <script src="../script/riwayat_admin.js?v=<?= time() ?>"></script>

    <!-- LOGOUT OVERLAY -->
    <div id="logout-overlay" data-logout-url="logout.php">
        <div class="card">
            <button class="close">×</button>

            <h2 class="title">Akhiri sesi administrasi? </h2>
            <p class="desc">Pastikan seluruh perubahan telah disimpan sebelum keluar. Sesi administrator akan berakhir sepenuhnya.</p>

            <div class="session">
                <div class="session-avatar"><?= strtoupper(substr($adminNama, 0, 1)) ?></div>
                <div>
                    <div class="session-name"><?= $adminNama ?></div>
                    <div class="session-role">Administrator</div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="btn-row">
                <button class="btn-modal btn-ghost">Batalkan</button>
                <button class="btn-modal btn-confirm">Ya, Keluar</button>
            </div>
        </div>
    </div>

    <script src="../script/logout-modal.js?v=<?= time() ?>"></script>
</body>
</html>
