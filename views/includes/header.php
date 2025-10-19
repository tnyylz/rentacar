<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Araç Kiralama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
   integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
   crossorigin=""/>
   <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
   <style>
            #map { height: 450px; }
            .fc { font-size: 0.85rem; }
            .fc .fc-toolbar-title { font-size: 1.2rem; line-height: 1.2; }
            .fc .fc-button { padding: 0.2rem 0.5rem; font-size: 0.8rem; }

            /* --- YENİ EKLENEN KARE TAKVİM STİLLERİ --- */
            .calendar-aspect-ratio-wrapper {
                position: relative;
                width: 100%; /* İçinde bulunduğu kolonun genişliğini %100 kapla */
                height: 0;
                padding-bottom: 100%; /* Genişliğin %100'ü kadar yükseklik oluşturarak KARE formunu yaratır */
            }
            
            .calendar-aspect-ratio-wrapper #calendar {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%; /* Kendisini sarmalayan kare alanın tamamını doldurur */
            }
            /* --- YENİ STİLLERİN SONU --- */
        </style>
   
</head>
<body>
    <div class="container py-4">
        <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
            <a href="/rentacar/public/home" class="fs-4 fw-bold text-dark text-decoration-none">
                Rent A Car
            </a>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="me-3">Hoş geldin, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>!</span>
                    <a href="/rentacar/public/my_reservations" class="btn btn-outline-primary">Rezervasyonlarım</a>
                    <a href="/rentacar/public/profile" class="btn btn-outline-secondary">Profilim</a> <a href="/rentacar/public/logout" class="btn btn-outline-danger">Çıkış Yap</a>
                <?php else: ?>
                    <a href="#" class="btn btn-outline-primary" id="loginModalBtn">Giriş Yap</a>
                    <a href="/rentacar/public/register" class="btn btn-primary">Kayıt Ol</a>
                <?php endif; ?>
            </nav>
        </header>
        <main>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>