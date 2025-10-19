<head>
    <style>
    /* Haritanın yüksekliğini, genişliğini ve görünümünü ayarlar */
    #map {
        height: 400px;
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    /* Diğer özel stilleriniz (takvim vb.) burada kalabilir */
    .fc { font-size: 0.85rem; }
    /* ... */
</style>
</head>


<?php require_once 'includes/header.php'; ?>

<div id="map" class="mb-4" style="position: relative;">
    <button id="locate-btn" class="btn btn-light shadow" style="position: absolute; top: 10px; left: 10px; z-index: 1000; border: 2px solid rgba(0,0,0,0.2);">
        📍 Konumumu Bul
    </button>
</div>

<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold">Hayalinizdeki Aracı Kiralayın</h1>
        <p class="col-md-8 fs-4">Aşağıdaki formu kullanarak istediğiniz tarihlerde müsait olan araçları arayın.</p>
    </div>
</div>

<div class="card card-body mb-5">
    <form action="/rentacar/public/home" method="GET">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <label for="start_date" class="form-label">Alış Tarihi</label>
                <input type="datetime-local" class="form-control" name="start_date" id="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <label for="end_date" class="form-label">Teslim Tarihi</label>
                <input type="datetime-local" class="form-control" name="end_date" id="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <label for="category_id" class="form-label">Kategori</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Tüm Kategoriler</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Müsait Araçları Bul</button>
            </div>
        </div>
    </form>
</div>

<div id="car-list-container">
    <?php 
        // Araç listesini ve sayfalamayı göstermek için partial dosyamızı dahil ediyoruz
        // DÜZELTME BU SATIRDA YAPILDI
        require_once __DIR__ . '/partials/car_list.php'; 
    ?>
</div>



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
    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>