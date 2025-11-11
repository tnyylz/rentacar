<?php require_once 'includes/header.php'; ?>

</div>

<div class="hero-section mb-5">
    
    <!-- Arka Plan Resmi -->
    <img src="/rentacar/public/images/heroes/inelsan.png" alt="İnelsan Rent A Car" class="hero-img">
    
    <!-- Arama Formu (Doğrudan hero-section içinde) -->
    <div class="hero-form-wrapper ">
        <ul class="nav nav-tabs mb-3" id="rentalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button" role="tab">Günlük</button>
            </li>
           
        </ul>
        
        <form action="/rentacar/public/home" method="GET">
            <div class="mb-2">
                <label for="pickup_location" class="form-label small mb-1 text-dark">Alış Noktası</label>
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

            <!-- Alış ve İade Tarihleri (Litepicker için) -->
            <div class="mb-2">
                <label for="pickup_date" class="form-label small mb-1 text-dark">Alış Tarihi</label>
                <input type="text" class="form-control fw-bold" id="pickup_date" name="pickup_date" placeholder="Tarih Aralığı Seçin" required>
            </div>
            <div class="mb-3">
                <label for="return_date" class="form-label small mb-1 text-dark">İade Tarihi</label>
                <input type="text" class="form-control fw-bold" id="return_date" name="return_date" placeholder="Tarih Aralığı Seçin" required>
            </div>

            <button type="submit" class="btn btn-discover w-100 py-2">Araçları Keşfet</button>
        </form>
    </div>
</div>



<div class="container">

<!-- Araç Listesi Alanı (AJAX ile güncellenecek olan kısım) -->
<div id="car-list-container">
    <?php 
        if (isset($isSearchSubmitted) && $isSearchSubmitted) {
            require_once __DIR__ . '/partials/car_list.php'; 
        }
    ?>
</div>




<!-- Araç Slider Alanı -->
<?php if (isset($featured_cars) && !empty($featured_cars)): ?>
<div class="container my-5">
    <h2 class="text-center mb-4" style="color: #FF6600; font-weight: 700;">Çeşit Çeşit Araç İnelsan'da, Hemen Kirala!</h2>
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




<!-- Harita Alanı -->
<div id="map" class="container mb-4" style="position: relative;">
    <button id="locate-btn" class="btn btn-light shadow" style="position: absolute; top: 10px; left: 10px; z-index: 1000; border: 2px solid rgba(0,0,0,0.2);">
        📍 Konumumu Bul
    </button>
</div>



 <hr class="my-5">
    <div class="container mb-5">
        <h2 class="text-center mb-5">Neden İnelsan Rent A Car?</h2>
        <div class="row text-center g-4">
            
            <!-- Madde 1: Geniş Lokasyon Ağı -->
            <div class="col-lg-3 col-md-6">
                <div class="why-us-icon-wrapper bg-primary-soft">
                    <!-- Bootstrap İkonu: Harita Pini -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-geo-alt-fill text-primary" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                </div>
                <h5 class="fw-bold mt-3">Geniş Lokasyon Ağı</h5>
                <p class="text-muted">İzmir'in dört bir yanında, havalimanı ve şehir merkezindeki ofislerimizle kolayca araç kiralayın.</p>
            </div>
            
            <!-- Madde 2: Yeni Araç Filosu -->
            <div class="col-lg-3 col-md-6">
                <div class="why-us-icon-wrapper bg-success-soft">
                    <!-- Bootstrap İkonu: Araba -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-car-front-fill text-success" viewBox="0 0 16 16">
                        <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM1.5 6a.5.5 0 0 0-.5.5v1.927a.5.5 0 0 0 .04.201l.47.882a.5.5 0 0 0 .46.29h10.06a.5.5 0 0 0 .46-.29l.47-.882a.5.5 0 0 0 .04-.201V6.5a.5.5 0 0 0-.5-.5h-11ZM16 6.5A1.5 1.5 0 0 0 14.5 5H14V3.5a1.5 1.5 0 0 0-3 0V5H5V3.5a1.5 1.5 0 0 0-3 0V5H1.5A1.5 1.5 0 0 0 0 6.5v1.927c0 .493.203.96.538 1.29l.47.882c.368.693 1.1 1.15 1.942 1.15h10.098c.842 0 1.574-.457 1.942-1.15l.47-.882c.335-.33.538-.797.538-1.29V6.5Z"/>
                    </svg>
                </div>
                <h5 class="fw-bold mt-3">Yeni ve Güvenli Filo</h5>
                <p class="text-muted">Tüm bakımları zamanında yapılmış, en yeni model ve farklı segmentlerdeki araçlar ile güvenli sürüşün keyfini çıkarın.</p>
            </div>
            
            <!-- Madde 3: Kolay Rezervasyon -->
            <div class="col-lg-3 col-md-6">
                <div class="why-us-icon-wrapper bg-warning-soft">
                    <!-- Bootstrap İkonu: Hızlı -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-speedometer2 text-warning" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM12.268 5.732a.5.5 0 0 1 0 .707l-.914.915a.5.5 0 1 1-.708-.708l.915-.914a.5.5 0 0 1 .707 0zM13.354 10.354a.5.5 0 0 1 0-.707l1.415-1.415a.5.5 0 1 1 .707.707l-1.415 1.415a.5.5 0 0 1-.707 0zM2.646 10.354a.5.5 0 0 1 .707 0l-1.415 1.415a.5.5 0 1 1-.707-.707l1.415-1.415a.5.5 0 0 1 0 .707zM8 1a7 7 0 0 0-7 7v.228a.58.58 0 0 0 .224.452l.993 1.05a.5.5 0 0 0 .753-.011l.22-.315.002-.001.006-.007.012-.016a.5.5 0 0 0 .023-.037l.033-.05.04-.06.048-.073.05-.077.053-.08a.5.5 0 0 0 .042-.098l.03-.075.016-.046A.5.5 0 0 0 4.6 9.5l-.348.497a.5.5 0 0 0 .142.7l1.011.889.012.012.024.02.04.03.05.02.07.01.093.004h3.946c.036 0 .072 0 .106-.005l.092-.004.07-.01.05-.02.04-.03.024-.02.012-.012 1.011-.889a.5.5 0 0 0 .142-.7l-.348-.497a.5.5 0 0 0-.092-.128l.016.046.03.075c.013.03.028.062.042.098l.053.08.05.077.048.073.04.06.033.05a.5.5 0 0 0 .023.037l.012.016.006.007.002.001.22.315a.5.5 0 0 0 .753.011l.993-1.05a.58.58 0 0 0 .224-.452V8a7 7 0 0 0-7-7zm0 12a5 5 0 0 1-5-5v-.228a.58.58 0 0 1 .224-.452l.993-1.05a.5.5 0 0 1 .753.011l.22.315.002.001.006.007.012.016.023.037.033.05.04.06.048.073.05.077.053.08.042.098.03.075.016.046A.5.5 0 0 1 4.6 9.5l-.348.497a.5.5 0 0 1-.142.7l-1.011.889-.012.012-.024.02-.04.03-.05.02-.07.01-.093.004H3a5 5 0 0 1 10 0h-.78c-.036 0-.072 0-.106-.005l-.092-.004-.07-.01-.05-.02-.04-.03-.024-.02-.012-.012-1.011-.889a.5.5 0 0 1-.142-.7l.348-.497a.5.5 0 0 1 .092-.128l-.016.046-.03.075c-.013.03-.028.062-.042.098l-.053.08-.05.077-.048.073-.04.06-.033.05a.5.5 0 0 1-.023.037l-.012.016-.006.007-.002.001-.22.315a.5.5 0 0 1-.753.011l-.993-1.05a.58.58 0 0 1-.224-.452V8a5 5 0 0 1-5 5z"/>
                    </svg>
                </div>
                <h5 class="fw-bold mt-3">Hızlı ve Kolay Kiralama</h5>
                <p class="text-muted">Kullanıcı dostu arayüzümüz ve akıllı arama formumuz ile saniyeler içinde aradığınız aracı bulun ve rezerve edin.</p>
            </div>
            
            <!-- Madde 4: 7/24 Destek -->
            <div class="col-lg-3 col-md-6">
                <div class="why-us-icon-wrapper bg-danger-soft">
                    <!-- Bootstrap İkonu: Kulaklık -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-headset text-danger" viewBox="0 0 16 16">
                        <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5zM6 13a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-1zm4-6V6a4 4 0 1 0-8 0v1h8z"/>
                    </svg>
                </div>
                <h5 class="fw-bold mt-3">7/24 Müşteri Desteği</h5>
                <p class="text-muted">Yolculuğunuzun her anında, sorularınızı yanıtlamak ve destek olmak için profesyonel ekibimizle yanınızdayız.</p>
            </div>
        </div>
    </div>
    





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
</div>








<?php require_once 'includes/footer.php'; ?>

