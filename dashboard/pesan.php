<?php
/**
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 *  RUMAH LAUNDRY â€” Pesan Pelanggan (List Data)
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 */
require_once 'config.php';
if (!isset($_SESSION['id'])) { header('Location: ../login.php'); exit; }

$adminNama = e($_SESSION['nama'] ?? 'Admin');

$data = $conn->query("SELECT * FROM pengiriman ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk â€” Rumah Laundry Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

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
                <li><a href="riwayat_admin.php" class="sidebar__link"><i class="fas fa-clock-rotate-left"></i><span>Riwayat Transaksi</span></a></li>
                <li><a href="pesan.php" class="sidebar__link active"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li>
            </ul>
            <span class="sidebar__label">LAINNYA</span>
            <ul class="sidebar__menu">
                <li><a href="../index.php" class="sidebar__link"><i class="fas fa-globe"></i><span>Lihat Website</span></a></li>
                <li><a href="logout.php" class="sidebar__link sidebar__link--logout" onclick="return confirm('Anda yakin ingin keluar?')"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <div class="overlay" id="overlay"></div>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar__left">
                <button class="topbar__toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="topbar__title"><h1>Pesan Masuk</h1><p>Pesan dari pelanggan website</p></div>
            </div>
        </header>

        <main class="content">
            <div class="table-section">
                <div class="table-section__header">
                    <div><h2 class="table-section__title">Daftar Pesan Pelanggan</h2><p>Total <?= $data->num_rows ?> pesan</p></div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr style="background-color: #1F2937; color: #ffffff !important;">
                                <th style="background-color: #1F2937; color: #ffffff !important;">No</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Nama</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">HP</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Alamat</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Pesan</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Cuci</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Layanan</th>
                                <th style="background-color: #1F2937; color: #ffffff !important;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($data->num_rows > 0): ?>
                                <?php $n=1; while($row = $data->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $n++ ?></td>
                                        <td><strong><?= e($row['nama']) ?></strong></td>
                                        <td><?= e($row['no_hp']) ?></td>
                                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($row['alamat']) ?>"><?= e($row['alamat']) ?></td>
                                        <td style="max-width:300px;white-space:normal;line-height:1.4;"><?= e($row['pesan']) ?></td>
                                        <td><?= e($row['jenis_pencucian']) ?></td>
                                        <td><?= e($row['jenis_layanan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_pengiriman'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" style="text-align:center;padding:40px;">Belum ada pesan masuk</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../script/dashboard.js"></script>
</body>
</html>
