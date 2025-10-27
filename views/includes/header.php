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

        /* === YENİ HERO STİLLERİ === */
        .hero-section {
            position: relative;
            height: 60vh; min-height: 450px;
            background-image: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1600&h=800&fit=crop');
            background-size: cover; background-position: center;
            border-radius: 1rem; padding: 2rem;
            display: flex; flex-direction: column; justify-content: space-between;
            color: white; overflow: hidden;
        }
        .hero-section::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.0) 40%, rgba(0,0,0,0.6) 100%); z-index: 1; }
        .hero-content, .hero-form-wrapper { position: relative; z-index: 2; }
        .hero-content h1 { font-size: 3.2rem; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        .hero-content p { font-size: 1.25rem; text-shadow: 0 1px 3px rgba(0,0,0,0.4); max-width: 500px; }
        
        /* === YENİ ARAMA FORMU STİLLERİ === */
        .hero-form-wrapper .card {
            background-color: #ffffff;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 0.5rem;
        }
        .hero-form-wrapper .form-control, .hero-form-wrapper .form-select {
            border: none;
            background-color: #f8f9fa;
            height: 48px;
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

