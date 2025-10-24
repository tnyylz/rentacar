<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Rent A Car - Admin Paneli</title>
    
    <link rel="icon" href="/rentacar/public/admin_assets/argon-dashboard-master/assets/img/brand/favicon.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
    <link rel="stylesheet" href="/rentacar/public/admin_assets/argon-dashboard-master/assets/vendor/nucleo/css/nucleo.css" type="text/css">
    <link rel="stylesheet" href="/rentacar/public/admin_assets/argon-dashboard-master/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" type="text/css">
    <link rel="stylesheet" href="/rentacar/public/admin_assets/argon-dashboard-master/assets/css/argon.css?v=1.2.0" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="g-sidenav-show bg-gray-200">
  <!-- Sol Menü (Sidenav) -->
 <nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
    <div class="scrollbar-inner">
      <div class="sidenav-header d-flex align-items-center">
        <a class="navbar-brand" href="/rentacar/public/">
          <img src="/rentacar/public/admin_assets/argon-dashboard-master/assets/img/brand/blue.png" class="navbar-brand-img" alt="Logo">
        </a>
      </div>
      <div class="navbar-inner">
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
          <!-- Menü Linkleri -->
          <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="/rentacar/public/admin/dashboard"><i class="ni ni-tv-2 text-primary"></i><span class="nav-link-text">Ana Sayfa</span></a></li>
            <li class="nav-item"><a class="nav-link" href="/rentacar/public/admin/cars"><i class="ni ni-delivery-fast text-orange"></i><span class="nav-link-text">Araç Yönetimi</span></a></li>
            <li class="nav-item"><a class="nav-link" href="/rentacar/public/admin/reservations"><i class="ni ni-calendar-grid-58 text-info"></i><span class="nav-link-text">Rezervasyonlar</span></a></li>
            <li class="nav-item"><a class="nav-link" href="/rentacar/public/admin/users"><i class="ni ni-single-02 text-yellow"></i><span class="nav-link-text">Kullanıcılar</span></a></li>
            <li class="nav-item"><a class="nav-link" href="/rentacar/public/admin/locations"><i class="ni ni-pin-3 text-primary"></i><span class="nav-link-text">Lokasyonlar</span></a></li>
            <li class="nav-item"><a class="nav-link" href="/rentacar/public/admin/categories"><i class="ni ni-bullet-list-67 text-default"></i><span class="nav-link-text">Kategoriler</span></a></li>
          </ul>
          <!-- Ayırıcı -->
          <hr class="my-3">
          <h6 class="navbar-heading p-0 text-muted"><span>Analiz</span></h6>
          <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="/rentacar/public/admin/reports"><i class="ni ni-chart-bar-32 text-success"></i><span class="nav-link-text">Raporlar</span></a></li>
          </ul>
          
          <!-- =================================== -->
          <!-- YENİ EKLENEN HESAP BÖLÜMÜ -->
          <!-- =================================== -->
          <hr class="my-3">
          <h6 class="navbar-heading p-0 text-muted">
            <span class="docs-normal">Hesap</span>
          </h6>
          <ul class="navbar-nav mb-md-3">
            <li class="nav-item">
              <a class="nav-link" href="/rentacar/public/admin/profile">
                <i class="ni ni-settings-gear-65 text-dark"></i>
                <span class="nav-link-text">Profilim</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/rentacar/public/logout">
                <i class="ni ni-user-run text-danger"></i>
                <span class="nav-link-text">Çıkış Yap</span>
              </a>
            </li>
          </ul>
          <!-- =================================== -->
          <!-- YENİ BÖLÜM SONU -->
          <!-- =================================== -->

        </div>
      </div>
    </div>
  </nav>
  
  <!-- Ana İçerik Alanı -->
  <div class="main-content" id="panel">
    <!-- Üst Menü (Topbar) -->
    <nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom">
      <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Search form -->
          <form class="navbar-search navbar-search-light form-inline mr-sm-3" id="navbar-search-main">
            <div class="form-group mb-0">
              <div class="input-group input-group-alternative input-group-merge">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input class="form-control" placeholder="Search" type="text">
              </div>
            </div>
            <button type="button" class="close" data-action="search-close" data-target="#navbar-search-main" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </form>
          <!-- Navbar links -->
          <ul class="navbar-nav align-items-center  ml-md-auto ">
            <li class="nav-item d-xl-none">
              <!-- Sidenav toggler -->
              <div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin" data-target="#sidenav-main">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </div>
            </li>
            <li class="nav-item d-sm-none">
              <a class="nav-link" href="#" data-action="search-show" data-target="#navbar-search-main">
                <i class="ni ni-zoom-split-in"></i>
              </a>
            </li>
          </ul>
          <ul class="navbar-nav align-items-center  ml-auto ml-md-0 ">
            <li class="nav-item dropdown">
              <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="media align-items-center">
                  <span class="avatar avatar-sm rounded-circle">
                    <?php 
                      // 1. Session'da resim yolu var mı diye kontrol et
                      $profile_pic = $_SESSION['profile_image_url'] ?? null;
                      
                      if ($profile_pic): // EĞER VARSA
                    ?>
                        <!-- 2. Doğrudan o yolu (tam yolu) yazdır -->
                        <img alt="Profil Resmi" src="<?php echo htmlspecialchars($profile_pic); ?>">
                    <?php else: // EĞER YOKSA (NULL ise) ?>
                        <!-- 3. Varsayılan avatarı göster -->
                        <img alt="Varsayılan Avatar" src="/rentacar/public/admin_assets/argon-dashboard-master/assets/img/theme/team-4.jpg">
                    <?php endif; ?>
                  </span>
                  <div class="media-body  ml-2  d-none d-lg-block">
                    <!-- Session'dan gelen Ad ve Soyadı yazdır -->
                    <span class="mb-0 text-sm  font-weight-bold"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                  </div>
                </div>
              </a>
              <div class="dropdown-menu  dropdown-menu-right ">
                <div class="dropdown-header noti-title">
                  <h6 class="text-overflow m-0">Menü</h6>
                </div>
                <a href="/rentacar/public/admin/profile" class="dropdown-item">
                  <i class="ni ni-single-02"></i>
                  <span>Profilim</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="/rentacar/public/logout" class="dropdown-item">
                  <i class="ni ni-user-run"></i>
                  <span>Çıkış Yap</span>
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- Üst Mavi Alan -->
   <div class="header bg-primary pb-6">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <!-- Sayfa başlığını Controller'dan gelen değişkene göre yazdır -->
              <h6 class="h2 text-white d-inline-block mb-0"><?php echo $page_title ?? 'Genel Bakış'; ?></h6>
              
              <!-- Breadcrumb'ı Controller'dan gelen diziye göre oluştur -->
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                  <li class="breadcrumb-item"><a href="/rentacar/public/admin/dashboard"><i class="fas fa-home"></i></a></li>
                  <?php if (isset($breadcrumbs)): ?>
                    <?php foreach ($breadcrumbs as $breadcrumb): ?>
                        <li class="breadcrumb-item <?php echo empty($breadcrumb['link']) ? 'active' : ''; ?>" aria-current="page">
                            <?php if (!empty($breadcrumb['link'])): ?>
                                <a href="<?php echo $breadcrumb['link']; ?>"><?php echo $breadcrumb['name']; ?></a>
                            <?php else: ?>
                                <?php echo $breadcrumb['name']; ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Sayfa İçeriği Başlangıcı -->
    <div class="container-fluid mt--6">