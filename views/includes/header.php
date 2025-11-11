<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araç Kiralama Projesi</title>
    
    <!-- Lumen Tema CSS'i -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css" rel="stylesheet">
    
    <!-- Diğer CSS Dosyaları -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    
    <!-- === YENİ LITEPCIKER CSS === -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css"/>

    <!-- Sayfaya Özel Stilleriniz -->
    <style>
      #map { height: 400px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .calendar-aspect-ratio-wrapper { position: relative; width: 100%; height: 0; padding-bottom: 100%; }
        .calendar-aspect-ratio-wrapper #calendar { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        .navbar .dropdown-avatar-toggle::after { display: none; } 
        .navbar .avatar { width: 36px; height: 36px; }
        .featured-car-img { height: 180px; object-fit: contain; width: 100%; background-color: #f8f9fa; }
        .featured-car-card { border: none; background: none; padding: 10px; transition: transform 0.2s ease-in-out; }
        .featured-car-card:hover { transform: translateY(-5px); }
        #featuredCarsCarousel .carousel-control-prev,
        #featuredCarsCarousel .carousel-control-next { width: 5%; }

        /* --- Hero Bölümü Stilleri (Yapısal) --- */
        .hero-section {
            position: relative; width: 100vw; height: 65vh; min-height: 550px;
            overflow: hidden; display: flex; align-items: flex-end;
        }
        .hero-img {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover; object-position: center; z-index: 1;
        }
        .hero-form-wrapper {
            position: relative; z-index: 2; background: #ffffff; border-radius: 12px;
            padding: 20px 25px; box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            width: 380px; margin-left: 10%; margin-bottom: 2rem;
        }

        /* --- YENİ EKLENEN FORM GÜZELLEŞTİRME --- */
        .hero-form-wrapper .nav-tabs {
            border-bottom: none; /* Alttaki çizgiyi kaldır */
        }
        .hero-form-wrapper .nav-tabs .nav-link {
            font-weight: 600; color: #555; border: none;
            background-color: #f0f0f0; /* Pasif tab rengi */
            margin-right: 5px; border-radius: 8px;
            padding: 0.6rem 1.2rem;
        }
        .hero-form-wrapper .nav-tabs .nav-link.active {
            background-color: #ff6600; /* Aktif tab rengi (Turuncu) */
            color: #fff;
        }
        .hero-form-wrapper .form-control,
        .hero-form-wrapper .form-select {
            padding: 0.8rem 1rem; height: 52px; font-weight: 600; font-size: 0.95rem;
            border: 1px solid #e0e0e0; border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }
        .hero-form-wrapper .form-control:focus,
        .hero-form-wrapper .form-select:focus {
            background-color: #ffffff; border-color: #ff6600; /* Odaklanınca turuncu kenarlık */
            box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.15);
        }
        .hero-form-wrapper .form-select {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23495057' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat; background-position: right 1rem center;
            background-size: 16px 12px; padding-right: 2.5rem; 
        }
        .hero-form-wrapper .form-control::placeholder {
            color: #9fa5ab; font-weight: 500;
        }
        .hero-form-wrapper .btn-discover {
            background-color: #ff6600; border-color: #ff6600; color: #fff;
            font-weight: bold; padding: 0.75rem; height: 52px;
            transition: all 0.2s ease;
        }
        .hero-form-wrapper .btn-discover:hover {
            background-color: #e55a00; border-color: #e55a00;
            box-shadow: 0 3px 10px rgba(255, 102, 0, 0.3);
        }
        
        /* --- YENİ EKLENEN TAKVİM GÜZELLEŞTİRME (Litepicker) --- */
        /* --- YENİ EKLENEN TAKVİM GÜZELLEŞTİRME (Litepicker) --- */
        .litepicker {
            font-family: 'Open Sans', sans-serif; /* Temanın fontuyla uyumlu yap */
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
            border: 1px solid #eee !important;
        }
        .litepicker .container__months .month-item-header {
            font-weight: 700 !important;
            font-size: 1rem !important;
            color: #333;
        }
        /* Ay/Yıl dropdown'larını formlarımızla aynı yap */
        .litepicker .month-item-header select {
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f8f9fa;
            padding: 0.3rem 0.5rem;
        }
        .litepicker .day-item.is-start-date {
            background-color: #ff6600 !important; /* Marka rengimiz (Turuncu) */
            color: #fff !important;
            border-radius: 1rem 0 0 1rem !important;
        }
        .litepicker .day-item.is-end-date {
            background-color: #ff6600 !important; /* Marka rengimiz (Turuncu) */
            color: #fff !important;
            border-radius: 0 1rem 1rem 0 !important;
        }
        .litepicker .day-item.is-in-range {
            background-color: rgba(255, 102, 0, 0.1) !important; /* Turuncunun açığı */
            color: #333 !important;
            border-radius: 0 !important;
        }
        .litepicker .day-item:hover {
            background-color: rgba(255, 102, 0, 0.3) !important;
            border-radius: 0.5rem !important;
        }
        .litepicker .day-item.is-today {
            color: #ff6600 !important;
            font-weight: 700;
        }
        .time-select {
            width: 110px !important;
            flex-basis: 110px !important;
            flex-grow: 0 !important;
        }
    </style>
</head>
<body>
    
    <!-- === DİNAMİK NAVİGASYON MENÜSÜ === -->
    <nav class="navbar navbar-expand-lg bg-light" data-bs-theme="light">
      <div class="container">
        <a class="navbar-brand fw-bold" href="/rentacar/public/home">İnelsan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
            <li class="nav-item">
              <a class="nav-link active" href="/rentacar/public/home">Ana Sayfa</a>
            </li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/rentacar/public/my_reservations">Rezervasyonlarım</a>
                </li>
                <?php 
                    $profile_pic = $_SESSION['profile_image_url'] ?? null;
                    $user_name = htmlspecialchars($_SESSION['first_name']);
                ?>
                <li class="nav-item dropdown">
                    <?php if ($profile_pic): ?>
                        <a class="nav-link dropdown-toggle d-flex align-items-center dropdown-avatar-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="avatar avatar-sm rounded-circle">
                                <img alt="Profil Resmi" src="<?php echo htmlspecialchars($profile_pic); ?>" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                            </span>
                            <span class="ms-2 d-none d-lg-block"><?php echo $user_name; ?></span>
                        </a>
                    <?php else: ?>
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $user_name; ?>
                        </a>
                    <?php endif; ?>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="/rentacar/public/profile">Profilim</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin'): ?>
                        <a class="dropdown-item" href="/rentacar/public/admin/dashboard">Admin Paneli</a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/rentacar/public/logout">Çıkış Yap</a>
                  </div>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="loginModalBtn">Giriş Yap</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/rentacar/public/register">Kayıt Ol</a>
                </li>
            <?php endif; ?>

          </ul>
        </div>
      </div>
    </nav>
    <!-- === MENÜ SONU === -->

    <!-- === SAYFA İÇERİĞİ BAŞLANGICI === -->
    <div class="container mt-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
            


