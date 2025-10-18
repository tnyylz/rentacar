<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Araç Kiralama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
   integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
   crossorigin=""/>
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