<?php require_once 'includes/header.php'; ?>

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
<h2><?php echo (empty(array_filter($_GET))) ? 'Tüm Müsait Araçlar' : 'Arama Sonuçları'; ?></h2>
<div class="row row-cols-1 row-cols-md-3 g-4 mt-2">
    <?php if (!empty($cars)): ?>
        <?php foreach ($cars as $car): ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
                        <p class="card-text">
                            <strong>Yıl:</strong> <?php echo htmlspecialchars($car['year']); ?><br>
                            <strong>Yakıt:</strong> <?php echo htmlspecialchars($car['fuel_type']); ?><br>
                            <strong>Vites:</strong> <?php echo htmlspecialchars($car['transmission_type']); ?>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5"><?php echo htmlspecialchars($car['daily_rate']); ?> TL/Gün</span>
                        <a href="/rentacar/public/car-detail?id=<?php echo $car['car_id']; ?>" class="btn btn-primary">Detayları Gör</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning">Aradığınız kriterlere uygun araç bulunamadı.</div>
        </div>
    <?php endif; ?>
</div>


<?php require_once 'includes/pagination.php'; ?>
<?php require_once 'includes/footer.php'; ?>