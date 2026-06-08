<?php

session_start();


$db_host = 'caboose.proxy.rlwy.net';
$db_user = 'root';
$db_pass = 'tVcKzxUBNfFskkwRmvLKbCBHrgZoSBvz';
$db_name = 'railway';
$db_port = 40556;

$conn = new mysqli(
    $db_host,
    $db_user,
    $db_pass,
    $db_name,
    $db_port
);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}


$conn->set_charset('utf8mb4');


$_migrateDb = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'transaksi' AND COLUMN_NAME = 'alamat'");
if ($_migrateDb && $_migrateDb->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE transaksi ADD COLUMN alamat VARCHAR(255) NOT NULL DEFAULT ''");
}
$_migrateDb = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'transaksi' AND COLUMN_NAME = 'whatsapp_sent'");
if ($_migrateDb && $_migrateDb->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE transaksi ADD COLUMN whatsapp_sent TINYINT(1) NOT NULL DEFAULT 0");
}
try {
    $conn->query("UPDATE transaksi SET no_hp = '' WHERE no_hp IS NULL");
    $conn->query("ALTER TABLE transaksi MODIFY COLUMN no_hp VARCHAR(50) NOT NULL DEFAULT ''");
} catch (Exception $e) { /* abaikan jika sudah sesuai */ }
$_migratePengiriman = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'pengiriman' AND COLUMN_NAME = 'kode_order'");
if ($_migratePengiriman && $_migratePengiriman->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE pengiriman ADD COLUMN kode_order VARCHAR(20) DEFAULT NULL");
}
$_migrateUser = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'user' AND COLUMN_NAME = 'no_hp'");
if ($_migrateUser && $_migrateUser->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE user ADD COLUMN no_hp VARCHAR(50) NOT NULL DEFAULT '' AFTER nama");
}
$_migrateLat = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'transaksi' AND COLUMN_NAME = 'latitude'");
if ($_migrateLat && $_migrateLat->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE transaksi ADD COLUMN latitude DOUBLE DEFAULT NULL");
    $conn->query("ALTER TABLE transaksi ADD COLUMN longitude DOUBLE DEFAULT NULL");
}
$_migrateLatP = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'pengiriman' AND COLUMN_NAME = 'latitude'");
if ($_migrateLatP && $_migrateLatP->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE pengiriman ADD COLUMN latitude DOUBLE DEFAULT NULL");
    $conn->query("ALTER TABLE pengiriman ADD COLUMN longitude DOUBLE DEFAULT NULL");
}
$conn->query("CREATE TABLE IF NOT EXISTS `profile` (
  `id_profile` INT NOT NULL AUTO_INCREMENT,
  `id_user`    INT NOT NULL,
  `nama`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `no_hp`      VARCHAR(50) NOT NULL DEFAULT '',
  `foto_profile` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_profile`),
  KEY `fk_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$_migrateIdUser = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'transaksi' AND COLUMN_NAME = 'id_user'");
if ($_migrateIdUser && $_migrateIdUser->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE transaksi ADD COLUMN id_user INT DEFAULT NULL AFTER id_laundry");
}

try {
    $conn->query("ALTER TABLE transaksi MODIFY COLUMN status ENUM('Baru','Diproses','Dikirim','Selesai','Dibatalkan','Dicuci','Dijemput','Menunggu') DEFAULT 'Baru'");
    $conn->query("UPDATE transaksi SET status = 'Baru' WHERE status IN ('Menunggu')");
    $conn->query("UPDATE transaksi SET status = 'Diproses' WHERE status = 'Dicuci'");
    $conn->query("UPDATE transaksi SET status = 'Dikirim' WHERE status = 'Dijemput'");
} catch (Exception $e) { /* abaikan jika sudah sesuai */ }

$_migrateAlamatP = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'pengiriman' AND COLUMN_NAME = 'alamat'");
if ($_migrateAlamatP && $_migrateAlamatP->fetch_row()[0] == 0) {
    $conn->query("ALTER TABLE pengiriman ADD COLUMN alamat VARCHAR(255) NOT NULL DEFAULT ''");
}


/*
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 *  3. CRUD HANDLERS (Create, Update, Delete)
 * â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
 */
/*CREATE / SIMPAN DATA*/
if (isset($_POST['addDataBtn'])) {
    if (!isLoggedIn()) { header('Location: ../login.php'); exit; }

    $kode_order = "RL-" . rand(1000, 9999);
    $nama                = $_POST['nama'];
    $no_hp               = substr(trim($_POST['no_hp'] ?? ''), 0, 50); 
    $jenis_pencucian     = $_POST['jenis_pencucian'];
    $jenis_layanan       = $_POST['jenis_layanan'];
    $tanggal_penjemputan = $_POST['tanggal_penjemputan'];
    $jam_penjemputan     = $_POST['jam_penjemputan'];
    $pesan               = $_POST['pesan'] ?? '';

    $status = "Baru";

    $stmt = $conn->prepare("INSERT INTO transaksi 
        (kode_order, nama, no_hp, jenis_pencucian, jenis_layanan, tanggal_penjemputan, jam_penjemputan, pesan, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssss', $kode_order, $nama, $no_hp, $jenis_pencucian, $jenis_layanan, $tanggal_penjemputan, $jam_penjemputan, $pesan, $status);

    if($stmt->execute()){
        $_SESSION['flash'] = ['type' => 'success', 'message' => "Transaksi berhasil ditambahkan. Kode: $kode_order"];
    }else{
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan data'];
    }
    $stmt->close();

    header("Location: table-op.php");
    exit;
}


/*UPDATE DATA*/
if (isset($_POST['update'])) {
    if (!isLoggedIn()) { header('Location: ../login.php'); exit; }

    $id                  = (int)($_POST['id_laundry'] ?? 0);
    $nama                = $_POST['nama'];
    $no_hp               = substr(trim($_POST['no_hp'] ?? ''), 0, 50);
    $jenis_pencucian     = $_POST['jenis_pencucian'];
    $jenis_layanan       = $_POST['jenis_layanan'];
    $tanggal_penjemputan = $_POST['tanggal_penjemputan'];
    $jam_penjemputan     = $_POST['jam_penjemputan'];
    $pesan               = $_POST['pesan'] ?? '';
    $alamat              = trim($_POST['alamat'] ?? '');
    $status              = $_POST['status'];

    /*Ambil data lama sebelum diupdate*/
    $stOld = $conn->prepare("SELECT status, whatsapp_sent, kode_order, no_hp, alamat FROM transaksi WHERE id_laundry = ? LIMIT 1");
    $stOld->bind_param('i', $id);
    $stOld->execute();
    $oldRow = $stOld->get_result()->fetch_assoc();
    $stOld->close();

    $stmt = $conn->prepare("UPDATE transaksi SET
        nama=?,
        no_hp=?,
        alamat=?,
        jenis_pencucian=?,
        jenis_layanan=?,
        tanggal_penjemputan=?,
        jam_penjemputan=?,
        pesan=?,
        status=?
        WHERE id_laundry=?");
    $stmt->bind_param('sssssssssi', $nama, $no_hp, $alamat, $jenis_pencucian, $jenis_layanan, $tanggal_penjemputan, $jam_penjemputan, $pesan, $status, $id);

    if($stmt->execute()){
        /*Notifikasi WhatsApp*/
        if ($status === 'Selesai' && ($oldRow['whatsapp_sent'] ?? 0) == 0) {
            $noHp = preg_replace('/[^0-9]/', '', $no_hp ?: ($oldRow['no_hp'] ?? ''));
            if (substr($noHp, 0, 1) === '0') $noHp = '62' . substr($noHp, 1);
            $kodeOrder = $oldRow['kode_order'] ?? '';
            $alamatWA  = $alamat ?: ($oldRow['alamat'] ?? '-');
            $pesanWA = "Halo $nama, pesanan laundry Anda dengan kode order $kodeOrder "
                     . "($jenis_layanan - $jenis_pencucian) telah *Selesai*. "
                     . "Silakan datang untuk mengambil pesanan Anda. "
                     . "Alamat: $alamatWA. "
                     . "Terima kasih telah menggunakan Rumah Laundry!";
            $_SESSION['wa_notify'] = [
                'nama' => $nama,
                'url'  => 'https://wa.me/' . $noHp . '?text=' . rawurlencode($pesanWA),
                'kode' => $kodeOrder,
            ];
            $conn->query("UPDATE transaksi SET whatsapp_sent = 1 WHERE id_laundry = $id");
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Transaksi berhasil diperbarui'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui data. Silakan coba lagi.'];
    }
    $stmt->close();

    if (in_array($status, ['Selesai', 'Dibatalkan'])) {
        header("Location: riwayat_admin.php");
    } else {
        header("Location: table-op.php");
    }
    exit;
}


/*DELETE DATA*/
if (isset($_GET['hapus'])) {
    if (!isLoggedIn()) { header('Location: ../login.php'); exit; }

    $id = (int)$_GET['hapus'];

    $stKode = $conn->prepare("SELECT kode_order FROM transaksi WHERE id_laundry = ? LIMIT 1");
    $stKode->bind_param('i', $id);
    $stKode->execute();
    $rowKode = $stKode->get_result()->fetch_assoc();
    $stKode->close();
    $kodeOrder = $rowKode['kode_order'] ?? null;

    $stmt = $conn->prepare("DELETE FROM transaksi WHERE id_laundry=?");
    $stmt->bind_param('i', $id);

    if($stmt->execute()){
        if ($kodeOrder) {
            $stDel = $conn->prepare("DELETE FROM pengiriman WHERE kode_order = ?");
            $stDel->bind_param('s', $kodeOrder);
            $stDel->execute();
            $stDel->close();
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Transaksi berhasil dihapus'];
    }else{
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data'];
    }
    $stmt->close();

    header("Location: table-op.php");
    exit;
}

/*
====================================
BULK DELETE DATA
====================================
*/
if (isset($_POST['bulkHapus']) && !empty($_POST['ids'])) {
    if (!isLoggedIn()) { header('Location: ../login.php'); exit; }
    $ids = array_filter(array_map('intval', $_POST['ids']));
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stKode = $conn->prepare("SELECT kode_order FROM transaksi WHERE id_laundry IN ($placeholders)");
        $stKode->bind_param($types, ...$ids);
        $stKode->execute();
        $resKode = $stKode->get_result();
        $kodeOrders = [];
        while ($rk = $resKode->fetch_assoc()) {
            if (!empty($rk['kode_order'])) $kodeOrders[] = $rk['kode_order'];
        }
        $stKode->close();

        $stmt = $conn->prepare("DELETE FROM transaksi WHERE id_laundry IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        if ($stmt->execute()) {
            $deleted = $stmt->affected_rows;
            if (!empty($kodeOrders)) {
                $kph = implode(',', array_fill(0, count($kodeOrders), '?'));
                $kpt = str_repeat('s', count($kodeOrders));
                $stDelP = $conn->prepare("DELETE FROM pengiriman WHERE kode_order IN ($kph)");
                $stDelP->bind_param($kpt, ...$kodeOrders);
                $stDelP->execute();
                $stDelP->close();
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => "$deleted transaksi berhasil dihapus."];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data.'];
        }
        $stmt->close();
    }
    header("Location: table-op.php");
    exit;
}

if (isset($_POST['bulkHapusRiwayat']) && !empty($_POST['ids'])) {
    if (!isLoggedIn()) { header('Location: ../login.php'); exit; }
    $ids = array_filter(array_map('intval', $_POST['ids']));
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stKode = $conn->prepare("SELECT kode_order FROM transaksi WHERE id_laundry IN ($placeholders)");
        $stKode->bind_param($types, ...$ids);
        $stKode->execute();
        $resKode = $stKode->get_result();
        $kodeOrders = [];
        while ($rk = $resKode->fetch_assoc()) {
            if (!empty($rk['kode_order'])) $kodeOrders[] = $rk['kode_order'];
        }
        $stKode->close();

        $stmt = $conn->prepare("DELETE FROM transaksi WHERE id_laundry IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        if ($stmt->execute()) {
            $deleted = $stmt->affected_rows;
            if (!empty($kodeOrders)) {
                $kph = implode(',', array_fill(0, count($kodeOrders), '?'));
                $kpt = str_repeat('s', count($kodeOrders));
                $stDelP = $conn->prepare("DELETE FROM pengiriman WHERE kode_order IN ($kph)");
                $stDelP->bind_param($kpt, ...$kodeOrders);
                $stDelP->execute();
                $stDelP->close();
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => "$deleted riwayat berhasil dihapus."];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus riwayat.'];
        }
        $stmt->close();
    }
    header("Location: riwayat_admin.php");
    exit;
}


function isLoggedIn(): bool {
    return isset($_SESSION['id']) && !empty($_SESSION['id']);
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Redirect if not admin role
 */
function requireAdmin(): void {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Escape string for safe HTML output
 */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
