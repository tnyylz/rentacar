<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araç Kiralama Projesi</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css"/>

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

        
        .hero-form-wrapper .nav-tabs {
            border-bottom: none; 
        }
        .hero-form-wrapper .nav-tabs .nav-link {
            font-weight: 600; color: #555; border: none;
            background-color: #f0f0f0; 
            margin-right: 5px; border-radius: 8px;
            padding: 0.6rem 1.2rem;
        }
        .hero-form-wrapper .nav-tabs .nav-link.active {
            background-color: #ff6600; 
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
            background-color: #ffffff; border-color: #ff6600;
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
        
      
        .litepicker {
            font-family: 'Open Sans', sans-serif; 
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
            border: 1px solid #eee !important;
        }
        .litepicker .container__months .month-item-header {
            font-weight: 700 !important;
            font-size: 1rem !important;
            color: #333;
        }
        
        .litepicker .month-item-header select {
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f8f9fa;
            padding: 0.3rem 0.5rem;
        }
        .litepicker .day-item.is-start-date {
            background-color: #ff6600 !important; 
            color: #fff !important;
            border-radius: 1rem 0 0 1rem !important;
        }
        .litepicker .day-item.is-end-date {
            background-color: #ff6600 !important; 
            color: #fff !important;
            border-radius: 0 1rem 1rem 0 !important;
        }
        .litepicker .day-item.is-in-range {
            background-color: rgba(255, 102, 0, 0.1) !important; 
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
        .how-it-works-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background-color: rgba(255, 102, 0, 0.1); /* Turuncu arka plan */
            color: #ff6600; /* Turuncu ikon rengi */
            font-size: 2.5rem; /* İkon boyutu */
            border-radius: 50%; /* Tam yuvarlak */
            margin-bottom: 1.5rem;
        }
         .cta-banner {
            background-color: #f8f9fa; /* Lumen temasının açık gri rengi */
            border-radius: 1rem; /* Sitedeki diğer öğelerle uyumlu yuvarlak köşe */
            padding: 3rem 2rem;
            text-align: center;
            border: 1px solid #e9ecef;
            transition: box-shadow 0.3s ease;
        }
        .cta-banner:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        .cta-banner h3 {
            font-weight: 700;
            color: #333;
        }
        .cta-banner p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 1.5rem;
        }
        .cta-banner .btn-primary {
            background-color: #ff6600; /* Marka rengin (Turuncu) */
            border-color: #ff6600;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            transition: all 0.2s ease;
        }
        .cta-banner .btn-primary:hover {
            background-color: #e55a00;
            border-color: #e55a00;
            transform: scale(1.03); /* Hafif büyüme efekti */
            box-shadow: 0 4px 15px rgba(255, 102, 0, 0.3);
        }
        .why-us-icon-wrapper {
            display: inline-flex; align-items: center; justify-content: center;
            width: 70px; height: 70px;
            margin-bottom: 1.5rem;
            border-radius: 50%;
        }
        .bg-primary-soft { background-color: rgba(13,110,253,0.1); }
        .bg-success-soft { background-color: rgba(25,135,84,0.1); }
        .bg-warning-soft { background-color: rgba(255,193,7,0.1); }
        .bg-danger-soft { background-color: rgba(220,53,69,0.1); }
        
        /* === YENİ CTA BANNER STİLLERİ === */
        .cta-banner {
            background-color: #f8f9fa; border-radius: 1rem; padding: 3rem 2rem;
            text-align: center; border: 1px solid #e9ecef;
            transition: box-shadow 0.3s ease;
        }
        .cta-banner:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .cta-banner h3 { font-weight: 700; color: #333; }
        .cta-banner .btn-primary {
            background-color: #ff6600; border-color: #ff6600; font-weight: 600;
            padding: 0.75rem 1.5rem; font-size: 1.1rem;
        }
        
        /* === YENİ SSS (FAQ) AKORDİYON STİLLERİ === */
        .faq-section .accordion-item {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: .75rem !important;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .faq-section .accordion-button {
            font-weight: 600;
            color: #2b2d42;
            background-color: #fff;
            border-radius: .75rem !important;
        }
        .faq-section .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #ff6600; /* Aktifken turuncu */
            box-shadow: none;
        }
        .faq-section .accordion-button:focus { box-shadow: none; }
        .faq-section .accordion-body { color: #555; }
        
        /* === YENİ GÜVEN ROZETLERİ STİLLERİ === */
        .trust-badge {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 1rem;
            transition: transform 0.2s ease;
        }
        .trust-badge:hover { transform: translateY(-5px); }
        .trust-badge .icon {
            font-size: 2.5rem;
            color: #ff6600; /* Turuncu */
            margin-right: 1.5rem;
        }
        .trust-badge h6 { font-weight: 700; margin-bottom: 0.25rem; }
        .trust-badge p { color: #6c757d; margin-bottom: 0; }
    </style>
</head>
<body>
    
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
            <li class="nav-item">
            <a class="nav-link" href="/rentacar/public/about">Hakkımızda</a>
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
    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
            


