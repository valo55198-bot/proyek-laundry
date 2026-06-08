<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rumah Laundry — Laundry Cepat, Bersih, dan Terpercaya</title>
    <meta name="description"
        content="Rumah Laundry menyediakan layanan laundry profesional, cepat, bersih, dan terpercaya. Laundry kiloan, satuan, dry cleaning, dan antar jemput." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/style.css?v=<?= filemtime('css/style.css') ?>" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="css/index-modal.css?v=<?= time() ?>" />
</head>

<body>

    <!-- ═══════════════════ NAVBAR ═══════════════════ -->
    <nav id="navbar" class="navbar">
        <div class="container navbar__inner">
            <a href="#home" class="navbar__brand">
                <img src="assets/img/RL.png" alt="Rumah Laundry" style="height:36px;width:auto;object-fit:contain;vertical-align:middle;margin-right:4px;"> Rumah Laundry
            </a>

            <button class="navbar__toggle" id="navToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>

            <ul class="navbar__menu" id="navMenu">
                <li><a href="#home" class="navbar__link active">Home</a></li>
                <li><a href="#service" class="navbar__link">Service</a></li>
                <li><a href="#review" class="navbar__link">Review</a></li>
                <li><a href="#lacak" class="navbar__link">Lacak Cucian</a></li>
                <li><a href="#contact" class="navbar__link">Contact</a></li>
                <li class="navbar__separator" aria-hidden="true"></li>
                <?php if (isset($_SESSION['id'])): ?>
                <li><a href="riwayat_pesanan.php" class="navbar__link"><i class="fas fa-clipboard-list"></i> Pesanan</a></li>
                <li class="navbar__user-area">
                    <a href="profile.php" class="navbar__avatar-link" title="Profil Saya">
                        <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                    </a>
                    <a href="logout.php" class="navbar__btn-logout logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
                <?php else: ?>
                <li>
                    <a href="login.php" class="navbar__btn-login">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section id="home" class="hero">
        <div class="hero__bg-shapes">
            <div class="hero__shape hero__shape--1"></div>
            <div class="hero__shape hero__shape--2"></div>
            <div class="hero__shape hero__shape--3"></div>
        </div>

        <div class="container hero__inner">
            <div class="hero__content" data-animate="fade-up">
                
                <h1 class="hero__title">Laundry Cepat, Bersih,<br />dan <span class="text-gradient">Terpercaya</span>
                </h1>
                <p class="hero__subtitle">Nikmati layanan laundry dengan kualitas terbaik. Pakaian Anda akan
                    kembali bersih, wangi, dan rapi — tanpa repot.</p>
                <div class="hero__buttons">
                    <a href="#service" class="btn btn--primary">
                        <i class="fas fa-shopping-bag"></i> Pesan Sekarang
                    </a>
                    <a href="#lacak" class="btn btn--outline">
                        <i class="fas fa-search"></i> Lacak Cucian
                    </a>
                </div>
                <div class="hero__stats">
                    <div class="hero__stat">
                        <strong>99+</strong>
                        <span>Pelanggan Puas</span>
                    </div>
                    <div class="hero__stat">
                        <strong>4.7★</strong>
                        <span>Rating</span>
                    </div>
                    <div class="hero__stat">
                        <strong>12 Jam</strong>
                        <span>Layanan</span>
                    </div>
                </div>
            </div>

            <div class="hero__visual" data-animate="fade-left">
                <div class="hero__illustration">
                    <div class="hero__float-card hero__float-card--1">
                        <i class="fas fa-check-circle"></i>
                        <span>Cucian Selesai!</span>
                    </div>
                    <div class="hero__float-card hero__float-card--2">
                        <i class="fas fa-star"></i>
                        <span>Rating 4.7</span>
                    </div>
                    <div class="hero__main-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="hero__orbit hero__orbit--1">
                        <i class="fas fa-soap"></i>
                    </div>
                    <div class="hero__orbit hero__orbit--2">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div class="hero__orbit hero__orbit--3">
                        <i class="fas fa-spray-can-sparkles"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ SERVICES ═══════════════════ -->
    <section id="service" class="services">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <span class="section-badge">Layanan Kami</span>
                <h2 class="section-title">Solusi Laundry <span class="text-gradient">Terlengkap</span></h2>
                <p class="section-subtitle">Kami menyediakan berbagai layanan laundry berkualitas tinggi untuk memenuhi
                    kebutuhan Anda.</p>
            </div>

            <div class="services__grid">
                <!-- Card 1 -->
                <div class="service-card" data-animate="fade-up" data-delay="0">
                    <div class="service-card__icon">
                        <i class="fas fa-weight-hanging"></i>
                    </div>
                    <h3 class="service-card__title">Laundry Kiloan</h3>
                    <p class="service-card__desc">Layanan cuci hemat per kilogram. Cocok untuk pakaian sehari-hari
                        dengan hasil bersih dan wangi.</p>
                    <div class="service-card__price">Mulai <strong>Rp 4.000</strong>/kg</div>
                    <a href="#contact" class="service-card__link">Pesan Sekarang <i class="fas fa-arrow-right"></i></a>
                </div>

                <!-- Card 2 -->
                <div class="service-card" data-animate="fade-up" data-delay="100">
                    <div class="service-card__icon">
                        <i class="fas fa-shirt"></i>
                    </div>
                    <h3 class="service-card__title">Laundry Satuan</h3>
                    <p class="service-card__desc">Perawatan khusus untuk setiap potong pakaian. Ideal untuk pakaian
                        formal dan bahan sensitif.</p>
                    <div class="service-card__price">Mulai <strong>Rp 15.000</strong>/pcs</div>
                    <a href="#contact" class="service-card__link">Pesan Sekarang <i class="fas fa-arrow-right"></i></a>
                </div>

                <!-- Card 3 -->
                <div class="service-card" data-animate="fade-up" data-delay="200">
                    <div class="service-card__icon">
                        <i class="fas fa-bed"></i>
                    </div>
                    <h3 class="service-card__title">Bed Cover</h3>
                    <p class="service-card__desc">Pembersihan bedcover dengan mesin dan deterjen khusus untuk menghilangkan kotoran.</p>
                    <div class="service-card__price">Mulai <strong>Rp 25.000</strong>/pcs</div>
                    <a href="#contact" class="service-card__link">Pesan Sekarang <i class="fas fa-arrow-right"></i></a>
                </div>

                <!-- Card 4 -->
                <div class="service-card" data-animate="fade-up" data-delay="300">
                    <div class="service-card__icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="service-card__title">Antar Jemput</h3>
                    <p class="service-card__desc">Layanan antar jemput cucian langsung ke rumah Anda. Hemat waktu, tanpa
                        perlu keluar rumah.</p>
                    <div class="service-card__price"><strong>Gratis</strong> ongkir*</div>
                    <a href="#contact" class="service-card__link">Pesan Sekarang <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ REVIEWS ═══════════════════ -->
    <section id="review" class="reviews-v2">
        <div class="container">
            <div class="reviews-v2__header" data-animate="fade-up">
                <h2 class="reviews-v2__title">Baca ulasan<br>Pilih laundry terpercaya.</h2>
                <div class="reviews-v2__trustpilot">
                    <span class="reviews-v2__rating">4.2/5</span>
                    <div class="reviews-v2__tp-brand">
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="reviews-v2__count">Berdasarkan 99+ ulasan</span>
                </div>
            </div>

            <div class="reviews-v2__content">
                <div class="reviews-v2__sidebar" data-animate="fade-right">
                    <div class="reviews-v2__quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <h3 class="reviews-v2__side-title">Apa yang<br> Mereka<br>Katakan.</h3>
                    
                    <div class="reviews-v2__nav">
                        <button class="reviews-v2__btn reviews-v2__btn--prev" aria-label="Previous">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="reviews-v2__pagination"></div>
                        <button class="reviews-v2__btn reviews-v2__btn--next" aria-label="Next">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <div class="reviews-v2__slider swiper" data-animate="fade-left">
                    <div class="swiper-wrapper">
                        <!-- Review 1 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Pelayanan sangat cepat dan hasilnya luar biasa bersih! Pakaian saya kembali seperti baru. Pasti akan jadi langganan."</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">AS</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Anisa Safira</strong>
                                        <span>1 week ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 2 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Layanan antar jemput sangat membantu! Tidak perlu repot keluar rumah, cucian dijemput dan diantar tepat waktu."</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">BR</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Budi Raharjo</strong>
                                        <span>2 weeks ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 3 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Dry cleaning mereka top banget! Jas saya terlihat seperti baru lagi. Harga juga sangat terjangkau untuk kualitas sebagus ini."</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">DW</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Dewi Wulandari</strong>
                                        <span>10 days ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 4 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Sudah berlangganan 2 tahun dan tidak pernah kecewa. Kualitas konsisten, staff ramah, dan wanginya tahan lama!"</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">FR</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Farhan Rizky</strong>
                                        <span>3 weeks ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 5 -->
                        <div class="swiper-slide">
                            <div class="review-card-v2">
                                <p class="review-card-v2__text">"Aplikasi tracking-nya sangat membantu. Saya bisa memantau status cucian kapan saja. Sangat modern dan praktis!"</p>
                                <div class="review-card-v2__rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <div class="review-card-v2__author">
                                    <div class="review-card-v2__avatar">MH</div>
                                    <div class="review-card-v2__meta">
                                        <strong>Maya Hartono</strong>
                                        <span>1 month ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ TRACKING ═══════════════════ -->
    <section id="lacak" class="tracking">
        <div class="container">
            <div class="section-header" data-animate="fade-up">
                <span class="section-badge">Lacak Cucian</span>
                <h2 class="section-title">Pantau <span class="text-gradient">Status Cucian</span> Anda</h2>
                <p class="section-subtitle">kode order untuk mengetahui status anda cucian secara real-time.
                </p>
            </div>

            <div class="tracking__box" data-animate="fade-up">
                <div class="tracking__input-group">
                    <div class="tracking__input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="trackInput" class="tracking__input"
                            placeholder="Masukkan kode order (cth: RL-1234)" autocomplete="off" />
                    </div>
                    <button class="btn btn--primary tracking__btn" id="trackBtn">
                        <i class="fas fa-location-arrow"></i> Lacak Sekarang
                    </button>
                </div>

                <!-- Loading -->
                <div class="tracking__loading" id="trackLoading">
                    <div class="tracking__spinner"></div>
                    <p>Sedang mencari data order Anda…</p>
                </div>

                <!-- Result -->
                <div class="tracking__result" id="trackResult">
                    <div class="tracking__result-header">
                        <div>
                            <h3 id="trackOrderId">Order #RL-1234</h3>
                            <p id="trackOrderDate">10 Februari 2026</p>
                        </div>
                        <span class="tracking__status-badge" id="trackStatusBadge">Diproses</span>
                    </div>

                    <div class="tracking__timeline" id="trackTimeline">
                        <!-- Step 1 -->
                        <div class="tracking__step completed">
                            <div class="tracking__step-icon"><i class="fas fa-clipboard-check"></i></div>
                            <div class="tracking__step-info">
                                <strong>Order Diterima</strong>
                                <span>Cucian sudah diterima di outlet</span>
                            </div>
                        </div>
                        
                        <!-- Step 2 -->
                        <div class="tracking__step active">
                            <div class="tracking__step-icon"><i class="fas fa-soap"></i></div>
                            <div class="tracking__step-info">
                                <strong>Diproses</strong>
                                <span>Cucian sedang dalam proses pencucian/setrika</span>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="tracking__step">
                            <div class="tracking__step-icon"><i class="fas fa-check-double"></i></div>
                            <div class="tracking__step-info">
                                <strong>Selesai</strong>
                                <span>Pesanan sudah selesai dan siap dijemput atau diantar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Not Found -->
                <div class="tracking__not-found" id="trackNotFound">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Order Tidak Ditemukan</h3>
                    <p>Pastikan kode order yang Anda masukkan benar. Coba lagi atau hubungi kami.</p>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ CONTACT ═══════════════════ -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="contact__wrapper" data-animate="fade-up">

                <!-- ═══ LEFT — Info Section ═══ -->
                <div class="contact__info">
                    <h2 class="contact__title">Diskusikan Kebutuhan <br>Laundry Anda</h2>
                    <p class="contact__desc">Apakah Anda mencari layanan laundry berkualitas tinggi yang disesuaikan dengan kebutuhan Anda? Hubungi kami sekarang.</p>

                    <div class="contact__details">
                        <div class="contact__item">
                            <div class="contact__icon">
                                <i class="fas fa-location-dot"></i>
                            </div>
                            <div class="contact__text">
                                <span>Alamat</span>
                                <p>Jl. P&K Lama, Sindangkerta, Kec. Lohbener, Kabupaten Indramayu, Jawa Barat 45252</p>
                            </div>
                        </div>

                        <div class="contact__item">
                            <div class="contact__icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="contact__text">
                                <span>WhatsApp</span>
                                <p>+62 812-3456-7890</p>
                            </div>
                        </div>

                        <div class="contact__item">
                            <div class="contact__icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact__text">
                                <span>E-mail</span>
                                <p>rumahlaundry@gmail.com</p>
                            </div>
                        </div>

                        <div class="contact__item">
                            <div class="contact__icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact__text">
                                <span>Jam Buka</span>
                                <p>Sen–Sab 07.00–21.00 &nbsp;|&nbsp; Min 08.00–18.00</p>
                            </div>
                        </div>
                    </div>

                    <div class="contact__map-desc">
                         <p class="contact__desc">Gunakan Google Maps untuk mendapatkan rute tercepat menuju toko laundry kami.</p>

                    </div>

                    <!-- Embed Google Maps -->
                    <div class="contact__map" style="border-radius: 12px; overflow: hidden; height: 200px; border: 1px solid rgba(255, 255, 255, 0.1);">
                        <!-- Nanti src iframe ini bisa diganti sendiri dengan link embed maps toko aslinya -->
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.021436195122!2d108.28572257486886!3d-6.391235393599273!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6eb88ad31f12b7%3A0xb55cc05ba2c921a5!2sJl.%20P%26K%20Lama%2C%20Sindangkerta%2C%20Kec.%20Lohbener%2C%20Kabupaten%20Indramayu%2C%20Jawa%20Barat%2045252!5e0!3m2!1sen!2sid!4v1780933410070!5m2!1sen!2sid" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <div class="swipe-wa" id="swipeWaContainer" style="margin-top: 1.5rem;">
                        <div class="swipe-wa__text" id="swipeWaText">Chat WhatsApp</div>
                        <div class="swipe-wa__thumb" id="swipeWaThumb">
                            <i class="fab fa-whatsapp" id="swipeWaIcon"></i>
                        </div>
                    </div>
                </div>

                <!-- ═══ RIGHT — Form Card ═══ -->
                <div class="contact__form-card">
                    <?php if (!isset($_SESSION['id'])): ?>
                    <!-- ── LOGIN PROMPT (belum login) ── -->
                    <div class="cfc__login-prompt">
                        <div class="cfc__lock-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3 class="cfc__prompt-title">Login Diperlukan</h3>
                        <p class="cfc__prompt-desc">Silakan login atau daftar terlebih dahulu untuk mengisi form pemesanan. Ini membantu kami memastikan keaslian pelanggan.</p>
                        <div class="cfc__prompt-buttons">
                            <a href="login.php" class="btn btn--primary cfc__prompt-btn">
                                <i class="fas fa-sign-in-alt"></i> Masuk
                            </a>
                            <a href="login.php#register" class="btn btn--outline cfc__prompt-btn" onclick="sessionStorage.setItem('toRegister','1')">
                                <i class="fas fa-user-plus"></i> Daftar
                            </a>
                        </div>
                    </div>
                    </div>
                    <?php else: ?>
                    <!-- ── FORM ORDER (sudah login) ── -->
                    <div id="formAlert" class="cfc__alert" style="display:none;"></div>

                    <form action="dashboard/proses_pesan.php" method="POST" class="cfc__form">

                        <!-- ═══ GROUP 1: Data Diri ═══ -->
                        <div class="cfc__group">
                            <div class="cfc__group-header">
                                <div class="cfc__group-icon"><i class="fas fa-user"></i></div>
                                <h3 class="cfc__group-title">Data Diri</h3>
                            </div>

                            <div class="cf-row-grid">
                                <div class="cfc__field">
                                    <label for="cf_nama">Nama Lengkap</label>
                                    <input id="cf_nama" type="text" name="nama" placeholder="Masukkan nama lengkap" required value="<?= htmlspecialchars($_SESSION['nama'] ?? '') ?>" />
                                </div>

                                <div class="cfc__field">
                                    <label for="cf_hp">Nomor Handphone</label>
                                    <input id="cf_hp" type="tel" name="no_hp" placeholder="08xxxxxxxxxx" required value="<?= htmlspecialchars($_SESSION['no_hp'] ?? '') ?>" />
                                </div>
                            </div>
                        </div>

                        <!-- ═══ GROUP 2: Detail Layanan ═══ -->
                        <div class="cfc__group">
                            <div class="cfc__group-header">
                                <div class="cfc__group-icon"><i class="fas fa-tshirt"></i></div>
                                <h3 class="cfc__group-title">Detail Layanan</h3>
                            </div>

                            <div class="cf-row-grid">
                                <div class="cfc__field">
                                    <label for="cf_cuci">Jenis Pencucian</label>
                                    <div class="cf-select-wrap">
                                        <select id="cf_cuci" name="jenis_pencucian" required>
                                            <option value="" disabled selected>Select...</option>
                                            <option value="Cuci Kering">Cuci Kering</option>
                                            <option value="Cuci Setrika">Cuci Setrika</option>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                <div class="cfc__field">
                                    <label for="cf_layanan">Jenis Layanan</label>
                                    <div class="cf-select-wrap">
                                        <select id="cf_layanan" name="jenis_layanan" required>
                                            <option value="" disabled selected>Select...</option>
                                            <option value="Reguler">Reguler</option>
                                            <option value="Express">Express</option>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="cf-row-grid">
                                <div class="cfc__field">
                                    <label for="cf_tanggal">Tanggal</label>
                                    <input id="cf_tanggal" type="date" name="tanggal_pengiriman" required />
                                </div>
                                <div class="cfc__field">
                                    <label for="cf_jam">Jam</label>
                                    <input id="cf_jam" type="time" name="jam_pengiriman" required />
                                </div>
                            </div>
                        </div>

                        <!-- ═══ GROUP 3: Lokasi & Alamat ═══ -->
                        <div class="cfc__group">
                            <div class="cfc__group-header">
                                <div class="cfc__group-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <h3 class="cfc__group-title">Lokasi & Alamat</h3>
                            </div>

                            <!-- Search Bar + GPS -->
                            <div class="cfc__map-toolbar">
                                <div class="cfc__map-search-wrap">
                                    <i class="fas fa-search cfc__map-search-icon"></i>
                                    <input type="text" id="mapSearchInput" placeholder="Cari nama jalan, desa, kecamatan..." autocomplete="off" class="cfc__map-search-input" />
                                    <div id="mapSearchSuggestions" class="cfc__map-suggestions"></div>
                                </div>
                                <button type="button" id="btnGeolocate" class="cfc__gps-btn">
                                    <i class="fas fa-crosshairs"></i> <span>GPS</span>
                                </button>
                            </div>

                            <!-- Map -->
                            <div id="map-picker-wrap" class="cfc__map-wrap">
                                <div id="orderMap" style="width:100%;height:220px;z-index:1;"></div>
                            </div>
                            <p class="cfc__map-tip"><i class="fas fa-info-circle"></i> <strong>Tips:</strong> Klik tombol GPS, lalu <strong>geser PIN</strong> ke titik tepat atap rumah Anda untuk akurasi 100%.</p>
                            <input type="hidden" name="latitude" id="cf_lat" value="" />
                            <input type="hidden" name="longitude" id="cf_lng" value="" />

                            <div class="cfc__field">
                                <label for="cf_alamat">Alamat</label>
                                <textarea id="cf_alamat" name="alamat" rows="2" placeholder="Masukkan alamat lengkap..." required></textarea>
                            </div>

                            <div class="cfc__field">
                                <label for="cf_pesan">Message</label>
                                <textarea id="cf_pesan" name="pesan" rows="3" placeholder="Type your message"></textarea>
                            </div>
                        </div>

                        <div class="swipe-submit" id="swipeSubmitContainer">
                            <div class="swipe-submit__text" id="swipeSubmitText">Geser untuk Kirim</div>
                            <div class="swipe-submit__thumb" id="swipeSubmitThumb">
                                <i class="fas fa-arrow-right" id="swipeSubmitIcon"></i>
                            </div>
                        </div>
                        <button type="submit" id="hiddenSubmitBtn" style="display: none;"></button>
                    </form>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══════════════════ FOOTER ═══════════════════ -->
    <footer class="footer">
        <div class="container footer__inner">
            <div class="footer__brand">
                <a href="#home" class="navbar__brand"><img src="assets/img/RL.png" alt="Rumah Laundry" style="height:30px;width:auto;object-fit:contain;vertical-align:middle;margin-right:4px;"> Rumah Laundry</a>
                <p>Layanan laundry profesional yang mengutamakan kebersihan, kecepatan, dan kepuasan pelanggan.</p>
            </div>

            <div class="footer__links">
                <h4>Menu</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#service">Service</a></li>
                    <li><a href="#review">Review</a></li>
                    <li><a href="#lacak">Lacak Cucian</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div class="footer__links">
                <h4>Layanan</h4>
                <ul>
                    <li><a href="#service">Laundry Kiloan</a></li>
                    <li><a href="#service">Laundry Satuan</a></li>
                    <li><a href="#service">Dry Cleaning</a></li>
                    <li><a href="#service">Antar Jemput</a></li>
                </ul>
            </div>

            <div class="footer__social">
                <h4>Ikuti Kami</h4>
                <div class="footer__social-icons">
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <div class="container">
                <p>&copy; 2026 Rumah Laundry. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="script/script.js?v=<?= filemtime('script/script.js') ?>"></script>

    <!-- ═══ LEAFLET MAP INIT ═══ -->
    <script src="script/index-map.js?v=<?= time() ?>"></script>

    <!-- LOGOUT OVERLAY -->
    <div id="logout-overlay" data-logout-url="logout.php">
        <div class="card">
            <button class="close">×</button>

            

            
            <h2 class="title">Kamu yakin ingin keluar?</h2>
            <p class="desc">Semua aktivitasmu telah tersimpan dengan aman. Kamu bisa kembali kapan saja.</p>

            <div class="session">
                <div class="session-avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'S', 0, 1)) ?></div>
                <div>
                    <div class="session-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></div>
                    <div class="session-role">Pengguna Aktif</div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="btn-row">
                <button class="btn-modal btn-ghost">Batal</button>
                <button class="btn-modal btn-confirm">Ya, Keluar</button>
            </div>
        </div>
    </div>

    <script src="script/logout-modal.js?v=<?= time() ?>"></script>
</body>

</html>
