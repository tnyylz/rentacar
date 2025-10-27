<?php require_once 'includes/header.php'; ?>


<div class="hero-section mb-5">
    <div class="hero-content">
        <h1 class="display-4">Her Yolda, Her Koşulda Yanında.</h1>
        <p class="lead">Geniş araç filosu ile Rent A Car, Türkiye'nin dört bir yanında.</p>
    </div>
    
    <div class="hero-form-wrapper">
        <div class="card card-body p-4">
            <!-- Formun action'ı /home'a gidiyor, bu AJAX script'i tarafından yakalanacak -->
            <form action="/rentacar/public/home" method="GET">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-3">
                        <label for="pickup_location" class="form-label small">Alış Noktası</label>
                        <select name="pickup_location_id" id="pickup_location" class="form-select fw-bold">
                            <option value="">Lokasyon Seçin...</option>
                            <?php
                            $conn = \App\Database::getInstance()->getConnection();
                            $locations = $conn->query("SELECT * FROM locations WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);
                            foreach ($locations as $location): ?>
                                <option value="<?php echo $location['location_id']; ?>" <?php echo (($_GET['pickup_location_id'] ?? '') == $location['location_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($location['location_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label for="pickup_date" class="form-label small">Alış Tarihi / Saati</label>
                        <div class="input-group">
                            <input type="text" class="form-control fw-bold" id="pickup_date" name="pickup_date" placeholder="Tarih Seçin" value="<?php echo htmlspecialchars($_GET['pickup_date'] ?? ''); ?>" required>
                            <select name="pickup_time" class="form-select time-select fw-bold">
                                <?php for ($h = 0; $h < 24; $h++): $time = sprintf('%02d:00', $h); ?>
                                    <option value="<?php echo $time; ?>" <?php echo (($_GET['pickup_time'] ?? '10:00') == $time) ? 'selected' : ''; ?>><?php echo $time; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="return_date" class="form-label small">İade Tarihi / Saati</label>
                        <div class="input-group">
                            <input type="text" class="form-control fw-bold" id="return_date" name="return_date" placeholder="Tarih Seçin" value="<?php echo htmlspecialchars($_GET['return_date'] ?? ''); ?>" required>
                             <select name="return_time" class="form-select time-select fw-bold">
                                <?php for ($h = 0; $h < 24; $h++): $time = sprintf('%02d:00', $h); ?>
                                    <option value="<?php echo $time; ?>" <?php echo (($_GET['return_time'] ?? '10:00') == $time) ? 'selected' : ''; ?>><?php echo $time; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1 d-grid">
                        <label for="" class="form-label small">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-lg">Bul</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Araç Listesi Alanı (AJAX ile güncellenecek olan kısım) -->
<div id="car-list-container">
    <?php 
        // Sadece bir arama yapıldıysa (sayfa ilk yüklendiğinde DEĞİL)
        if (isset($isSearchSubmitted) && $isSearchSubmitted) {
            // Araç listesi parçasını yükle
            require_once __DIR__ . '/partials/car_list.php'; 
        }
    ?>
</div>



<!-- Harita Alanı -->
<div id="map" class="mb-4" style="position: relative;">
    <button id="locate-btn" class="btn btn-light shadow" style="position: absolute; top: 10px; left: 10px; z-index: 1000; border: 2px solid rgba(0,0,0,0.2);">
        📍 Konumumu Bul
    </button>
</div>

<!-- Öne Çıkan Araçlar (Slider) -->
<?php if (isset($featured_cars) && !empty($featured_cars)): ?>
<div class="container my-5">
    <h2 class="text-center mb-4" style="color: #FF6600; font-weight: 700;">Çeşit Çeşit Araç Rent A Car'da, Hemen Kirala!</h2>
    <div id="featuredCarsCarousel" class="carousel slide carousel-dark" data-bs-ride="carousel">
        <?php $car_chunks = array_chunk($featured_cars, 4); ?>
        <div class="carousel-indicators">
            <?php foreach ($car_chunks as $index => $chunk): ?>
                <button type="button" data-bs-target="#featuredCarsCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo ($index === 0) ? 'active' : ''; ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($car_chunks as $index => $car_chunk): ?>
                <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                    <div class="row justify-content-center">
                        <?php foreach ($car_chunk as $car): ?>
                            <div class="col-lg-3 col-md-6"> 
                                <div class="card featured-car-card h-100">
                                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" class="card-img-top featured-car-img" alt="<?php echo htmlspecialchars($car['brand']); ?>">
                                    <div class="card-body text-center">
                                        <h6 class="card-title fw-bold"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h6>
                                        <p class="card-text text-muted small"><?php echo htmlspecialchars($car['transmission_type'] . ' ' . $car['year']); ?></p>
                                        <a href="/rentacar/public/car_detail?id=<?php echo $car['car_id']; ?>" class="btn btn-sm btn-outline-primary">Aracı Gör</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarsCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#featuredCarsCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></button>
    </div>
</div>
<?php endif; ?>
<!-- Bölüm Sonu -->

<!-- Hero Arama Formu -->


<!-- Yorum Slider'ı -->
<?php if (isset($testimonials) && !empty($testimonials)): ?>
<hr class="my-5">
<h2 class="text-center mb-4">Müşteri Yorumları</h2>
<div id="testimonialCarousel" class="carousel slide carousel-dark" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php foreach ($testimonials as $index => $testimonial): ?>
            <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                <div class="d-flex justify-content-center">
                    <div class="col-lg-8 text-center">
                        <p class="fs-5 fst-italic">"<?php echo htmlspecialchars($testimonial['comment']); ?>"</p>
                        <p><strong>- <?php echo htmlspecialchars($testimonial['first_name'] . ' ' . substr($testimonial['last_name'], 0, 1) . '.'); ?></strong></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></button>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

