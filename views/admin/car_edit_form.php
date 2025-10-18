<?php require_once 'includes/header.php'; ?>

<h1 class="h2">Aracı Düzenle: <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>

<form action="/rentacar/public/admin/cars/update" method="POST" class="mt-4">
    <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="brand" class="form-label">Marka</label>
            <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($car['brand']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="model" class="form-label">Model</label>
            <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="year" class="form-label">Yıl</label>
            <input type="number" class="form-control" id="year" name="year" value="<?php echo htmlspecialchars($car['year']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="license_plate" class="form-label">Plaka</label>
            <input type="text" class="form-control" id="license_plate" name="license_plate" value="<?php echo htmlspecialchars($car['license_plate']); ?>" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="daily_rate" class="form-label">Günlük Ücret (TL)</label>
            <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" value="<?php echo htmlspecialchars($car['daily_rate']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>" <?php echo ($car['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">    
        <div class="col-md-6 mb-3">
                <label for="current_location_id" class="form-label">Güncel Lokasyon</label>
                <select class="form-select" id="current_location_id" name="current_location_id" required>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?php echo $location['location_id']; ?>" <?php echo ($car['current_location_id'] == $location['location_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($location['location_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
        </div>
        <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Durum</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Müsait" <?php echo ($car['status'] == 'Müsait') ? 'selected' : ''; ?>>Müsait</option>
                    <option value="Kiralandı" <?php echo ($car['status'] == 'Kiralandı') ? 'selected' : ''; ?>>Kiralandı</option>
                    <option value="Bakımda" <?php echo ($car['status'] == 'Bakımda') ? 'selected' : ''; ?>>Bakımda</option>
                </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
    <a href="/rentacar/public/admin/cars" class="btn btn-secondary">İptal</a>
</form>

<?php require_once 'includes/footer.php'; ?>