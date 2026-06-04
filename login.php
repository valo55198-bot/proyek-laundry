<?php
/**
 * ═══════════════════════════════════════════════════════
 *  RUMAH LAUNDRY — Login & Register (Redesigned)
 *  Dark premium split-panel layout
 * ═══════════════════════════════════════════════════════
 */

require_once 'dashboard/config.php';

// If already logged in, redirect based on role
if (isset($_SESSION['id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';
$success = '';

// Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error = 'Email dan password wajib diisi';
        } else {
            $stmt = $conn->prepare("SELECT iduser, nama, no_hp, email, password, role FROM user WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['id'] = $user['iduser'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['no_hp'] = $user['no_hp'] ?? '';
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    if ($user['role'] === 'admin') {
                        header('Location: dashboard/dashboard.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit;
                } else {
                    $error = 'Password salah!';
                }
            } else {
                $error = 'Email tidak ditemukan!';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['register'])) {
        $nama = trim($_POST['nama']);
        $no_hp = trim($_POST['no_hp']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($nama) || empty($email) || empty($password)) {
            $error = 'Semua field wajib diisi';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter';
        } else {
            // Check duplicate email
            $stmt = $conn->prepare("SELECT iduser FROM user WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Email sudah terdaftar. Silakan login.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt_reg = $conn->prepare("INSERT INTO user (nama, no_hp, email, password, role) VALUES (?, ?, ?, ?, 'user')");
                $stmt_reg->bind_param('ssss', $nama, $no_hp, $email, $hashedPassword);
                
                if ($stmt_reg->execute()) {
                    $success = 'Registrasi berhasil! Silakan login.';
                } else {
                    $error = 'Gagal mendaftar. Coba lagi.';
                }
                $stmt_reg->close();
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Rumah Laundry — Login & Register</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/login.css?v=<?= filemtime('css/login.css') ?>">
</head>
<body>
<div class="scene">
<div class="card <?php echo (isset($_POST['register']) && empty($success)) ? 'reg' : ''; ?>" id="card">

  <!-- ═══ IMAGE PANEL (left) ═══ -->
  <div class="img-panel">
    <div class="img-panel__img-wrap">
      <img class="img-panel__img" src="assets/img/gambar login.png" alt="Rumah Laundry — Layanan Premium"/>

      <div class="img-panel__body">
       

        <!-- Bottom quote -->
        <div class="img-panel__quote">
          <h2>Bersih, Wangi Cepat <br> Aman, Tepat Waktu.</h2>
          <div class="img-panel__dots">
            <div class="img-panel__dot img-panel__dot--active"></div>
            <div class="img-panel__dot"></div>
            <div class="img-panel__dot"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ FORM PANEL (right) ═══ -->
  <div class="form-panel-wrap">
    <div class="forms-container">

      <!-- LOGIN VIEW -->
      <div class="form-view" id="viewLogin">
        <h1 class="form-heading">Masuk ke Akun</h1>
        <p class="form-sub">Selamat datang kembali! Silakan masuk untuk melanjutkan.</p>

        <form method="POST" action="">
          <div class="field">
            <label>Email</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="2" y="5" width="16" height="12" rx="2.5"/>
                <path d="M2 8l8 5 8-5"/>
              </svg>
              <input type="email" name="email" placeholder="nama@email.com" required autocomplete="email" value="<?php echo isset($_POST['email']) && (isset($_POST['login']) || !empty($success)) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
            </div>
          </div>
          <div class="field">
            <label>Password</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="5" y="9" width="10" height="8" rx="2"/>
                <path d="M7 9V7a3 3 0 016 0v2"/>
                <circle cx="10" cy="13.5" r="1.2" fill="currentColor" stroke="none"/>
              </svg>
              <input type="password" name="password" id="lPw" placeholder="Masukkan password" required/>
              <button type="button" class="pw-btn" onclick="tPw('lPw')">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                  <path d="M2 10s3-5.5 8-5.5S18 10 18 10s-3 5.5-8 5.5S2 10 2 10z"/>
                  <circle cx="10" cy="10" r="2.2"/>
                </svg>
              </button>
            </div>
          </div>
          <button type="submit" name="login" class="btn-primary"><span>Masuk</span></button>
          <div class="sw">
            Belum punya akun?
            <button type="button" onclick="go('register')">Daftar sekarang</button>
          </div>
        </form>
      </div>

      <!-- REGISTER VIEW -->
      <div class="form-view" id="viewRegister">
        <h1 class="form-heading">Buat Akun Baru</h1>
        <p class="form-sub">Daftar sekarang dan mulai nikmati layanan laundry premium kami.</p>

        <form method="POST" action="">
          <div class="field">
            <label>Nama Lengkap</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <circle cx="10" cy="6.5" r="3.5"/>
                <path d="M3 18c0-3.87 3.13-7 7-7s7 3.13 7 7"/>
              </svg>
              <input type="text" name="nama" placeholder="Nama lengkap Anda" required value="<?php echo (isset($_POST['nama']) && empty($success)) ? htmlspecialchars($_POST['nama']) : ''; ?>"/>
            </div>
          </div>
          <div class="field">
            <label>Nomor HP</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="5" y="2" width="10" height="16" rx="2.5"/>
                <circle cx="10" cy="15.5" r="0.8" fill="currentColor" stroke="none"/>
              </svg>
              <input type="tel" name="no_hp" placeholder="+62 812-3456-7890" value="<?php echo (isset($_POST['no_hp']) && empty($success)) ? htmlspecialchars($_POST['no_hp']) : ''; ?>"/>
            </div>
          </div>
          <div class="field">
            <label>Email</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="2" y="5" width="16" height="12" rx="2.5"/>
                <path d="M2 8l8 5 8-5"/>
              </svg>
              <input type="email" name="email" placeholder="nama@email.com" required value="<?php echo isset($_POST['email']) && isset($_POST['register']) && empty($success) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
            </div>
          </div>
          <div class="field">
            <label>Password</label>
            <div class="inp-wrap">
              <svg class="ic" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="5" y="9" width="10" height="8" rx="2"/>
                <path d="M7 9V7a3 3 0 016 0v2"/>
                <circle cx="10" cy="13.5" r="1.2" fill="currentColor" stroke="none"/>
              </svg>
              <input type="password" name="password" id="rPw" placeholder="Min. 6 karakter" required/>
              <button type="button" class="pw-btn" onclick="tPw('rPw')">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                  <path d="M2 10s3-5.5 8-5.5S18 10 18 10s-3 5.5-8 5.5S2 10 2 10z"/>
                  <circle cx="10" cy="10" r="2.2"/>
                </svg>
              </button>
            </div>
          </div>
          
          <button type="submit" name="register" class="btn-primary"><span>Daftar Sekarang</span></button>
          <div class="sw">
            Sudah punya akun?
            <button type="button" onclick="go('login')">Masuk di sini</button>
          </div>
        </form>
      </div>

    </div>
  </div>

</div>
</div>

<script src="script/login.js?v=<?= time() ?>"></script>
<script>

// ── PHP Integration ──
<?php if (!empty($success)): ?>
  showToast(<?php echo json_encode($success); ?>, 'success');
<?php endif; ?>
<?php if (!empty($error)): ?>
  showToast(<?php echo json_encode($error); ?>, 'error');
<?php endif; ?>
</script>
</body>
</html>
