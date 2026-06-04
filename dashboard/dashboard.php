<?php

require_once 'config.php';

requireAdmin();

$recentPesan = $conn->query("
    SELECT nama, pesan, tanggal_pengiriman 
    FROM pengiriman 
    ORDER BY tanggal_pengiriman DESC 
    LIMIT 5
");


$totalTransaksi = $conn->query("SELECT COUNT(*) AS c FROM transaksi WHERE status IN ('Baru','Diproses','Dikirim')")->fetch_assoc()['c'];
$totalPesan     = $conn->query("SELECT COUNT(*) AS c FROM pengiriman")->fetch_assoc()['c'];
$totalDiproses  = $conn->query("SELECT COUNT(*) AS c FROM transaksi WHERE status = 'Diproses'")->fetch_assoc()['c'];
$totalSelesai   = $conn->query("SELECT COUNT(*) AS c FROM transaksi WHERE status = 'Selesai'")->fetch_assoc()['c'];

$recentTransaksi = $conn->query("SELECT * FROM transaksi WHERE status IN ('Baru','Diproses','Dikirim') ORDER BY id_laundry DESC LIMIT 5");

$userNama = e($_SESSION['nama'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard â€” Rumah Laundry Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/dashboard-modal.css?v=<?= time() ?>">
</head>
<body>

    <!-- SIDEBAR -->
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
                <li><a href="dashboard.php" class="sidebar__link active"><i class="fas fa-th-large"></i><span>Dashboard</span></a></li>
                <li><a href="table-op.php" class="sidebar__link"><i class="fas fa-receipt"></i><span>Transaksi</span></a></li>
                <li><a href="riwayat_admin.php" class="sidebar__link"><i class="fas fa-clock-rotate-left"></i><span>Riwayat Transaksi</span></a></li>
                <li><a href="pesan.php" class="sidebar__link"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li> 
            </ul>
           <span class="sidebar__label">LAINNYA</span> 
            <ul class="sidebar__menu">
                <li><a href="../index.php" class="sidebar__link"><i class="fas fa-globe"></i><span>Lihat Website</span></a></li>
                <li><a href="logout.php" class="sidebar__link sidebar__link--logout logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <div class="overlay" id="overlay"></div>

    <div class="main-wrapper" id="mainWrapper">
        <header class="topbar">
            <div class="topbar__left">
                <button class="topbar__toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar__title">
                    <h1>Dashboard</h1>
                    <p>Selamat datang kembali, <?= $userNama ?></p>
                </div>
            </div>
            <div class="topbar__right">
                <div class="topbar__profile" id="profileToggle">
                    <div class="topbar__avatar"><?= strtoupper(substr($userNama, 0, 1)) ?></div>
                    <span class="topbar__name"><?= $userNama ?></span>
                    <i class="fas fa-chevron-down"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="topbar__dropdown">
                        <a href="../index.php"><i class="fas fa-globe"></i> Lihat Website</a>
                        <div class="topbar__dropdown-divider"></div>
                        <a href="logout.php" class="logout-btn topbar__dropdown--danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <!-- Summary Cards -->
            <div class="cards-grid">
                <div class="summary-card" data-animate="fade-up">
                    <div class="summary-card__icon summary-card__icon--total"><i class="fas fa-layer-group"></i></div>
                    <div class="summary-card__info">
                        <span class="summary-card__label">Total Transaksi</span>
                        <h2 class="summary-card__value"><?= $totalTransaksi ?></h2>
                    </div>
                </div>
                <div class="summary-card" data-animate="fade-up" data-delay="100">
                    <div class="summary-card__icon summary-card__icon--process"><i class="fas fa-envelope-open-text"></i></div>
                    <div class="summary-card__info">
                        <span class="summary-card__label">Pesan Masuk</span>
                        <h2 class="summary-card__value"><?= $totalPesan ?></h2>
                    </div>
                </div>
                <div class="summary-card" data-animate="fade-up" data-delay="300">
                    <div class="summary-card__icon summary-card__icon--wait"><i class="fas fa-clock"></i></div>
                    <div class="summary-card__info">
                        <span class="summary-card__label">Status Di Proses</span>
                        <h2 class="summary-card__value"><?= $totalDiproses ?></h2>
                    </div>
                </div>

                <div class="summary-card" data-animate="fade-up" data-delay="400">
                    <div class="summary-card__icon summary-card__icon--done"><i class="fas fa-check-circle"></i></div>
                    <div class="summary-card__info">
                        <span class="summary-card__label">Status Selesai</span>
                        <h2 class="summary-card__value"><?= $totalSelesai ?></h2>
                    </div>
                </div>
            </div>

            

            <!-- Recent Transactions -->
            <div class="table-section" style="margin-top: 24px;" data-animate="fade-up">
                <div class="table-section__header">
                    <div>
                        <h2 class="table-section__title">Transaksi Terbaru</h2>
                        <p class="table-section__subtitle">5 data terakhir</p>
                    </div>
                    <a href="table-op.php" class="btn btn--outline">Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr style="background-color: #1F2937;">
                                <th style="background-color: #1F2937; color: #ffffff !important;">Kode</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Nama</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Pesan</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Cuci</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Layanan</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentTransaksi->num_rows > 0): ?>
                                <?php while($row = $recentTransaksi->fetch_assoc()): ?>
                                    <tr>
                                        <td><code><?= e($row['kode_order']) ?></code></td>
                                        <td><strong><?= e($row['nama']) ?></strong></td>
                                        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($row['pesan']) ?></td>
                                        <td><?= e($row['jenis_pencucian']) ?></td>
                                        <td><?= e($row['jenis_layanan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_penjemputan'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center;padding:40px;">Belum ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-section" style="margin-top: 24px;" data-animate="fade-up">
                <div class="table-section__header">
                    <div>
                        <h2 class="table-section__title">Pesan Pelanggan</h2>
                        <p class="table-section__subtitle">Pesan terbaru dari website</p>
                    </div>
                    <a href="pesan.php" class="btn btn--outline">Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr style="background-color: #1F2937;">
                                <th style="background-color: #1F2937; color: #ffffff !important;">Nama</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Pesan</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentPesan->num_rows > 0): ?>
                                <?php while($row = $recentPesan->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?= e($row['nama']) ?></strong></td>
                                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($row['pesan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_pengiriman'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align:center;padding:40px;">Belum ada pesan</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script src="../script/dashboard.js"></script>

    <!-- LOGOUT OVERLAY -->
    <div id="logout-overlay" data-logout-url="logout.php">
        <div class="card">
            <button class="close">Ã—</button>

            <h2 class="title">Akhiri sesi administrasi? </h2>
            <p class="desc">Pastikan seluruh perubahan telah disimpan sebelum keluar. Sesi administrator akan berakhir sepenuhnya.</p>

            <div class="session">
                <div class="session-avatar"><?= strtoupper(substr($userNama, 0, 1)) ?></div>
                <div>
                    <div class="session-name"><?= $userNama ?></div>
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
