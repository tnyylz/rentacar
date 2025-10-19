<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    </head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar vh-100 border-end">
            <div class="position-sticky pt-3">
                <h4 class="px-3">Yönetim Paneli</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="/rentacar/public/admin/dashboard">
                            Ana Sayfa (Dashboard)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rentacar/public/admin/cars">
                            Araç Yönetimi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rentacar/public/admin/reservations">
                            Rezervasyonlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rentacar/public/admin/users">
                            Kullanıcılar
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="/rentacar/public/admin/locations">
                            Lokasyonlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rentacar/public/admin/categories">
                            Kategoriler
                        </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/rentacar/public/admin/reports">
                        Raporlar
                    </a>
                </li>
                </ul>

                <hr>

                <div class="px-3 pb-3 mt-auto">
                    <span class="d-block mb-2">Hoş geldin, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>!</span>
                    <a href="/rentacar/public/logout" class="btn btn-danger btn-sm">Güvenli Çıkış</a>
                </div>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

        <?php
            if (isset($_SESSION['message'])) {
                // Mesajın türünü de session'dan alalım (success, danger, info vb.)
                $message_type = $_SESSION['message_type'] ?? 'info';
                ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; // Mesajı HTML olarak yazdır ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php
                // Mesajı gösterdikten sonra session'dan temizle ki tekrar görünmesin
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>