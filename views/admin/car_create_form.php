<?php require_once 'includes/header.php'; ?>

<h1 class="h2">Yeni Araç Ekle</h1>

<form action="/rentacar/public/admin/cars/store" method="POST" class="mt-4">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="brand" class="form-label">Marka</label>
            <input type="text" class="form-control" id="brand" name="brand" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="model" class="form-label">Model</label>
            <input type="text" class="form-control" id="model" name="model" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="year" class="form-label">Yıl</label>
            <input type="number" class="form-control" id="year" name="year" min="1990" max="<?php echo date('Y') + 1; ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="license_plate" class="form-label">Plaka</label>
            <input type="text" class="form-control" id="license_plate" name="license_plate" required>
        </div>
    </div>
     <div class="row">
        <div class="col-md-6 mb-3">
            <label for="daily_rate" class="form-label">Günlük Ücret (TL)</label>
            <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="" disabled selected>Kategori Seçin...</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="fuel_type" class="form-label">Yakıt Tipi</label>
            <select class="form-select" id="fuel_type" name="fuel_type" required>
                <option value="Benzin">Benzin</option>
                <option value="Dizel">Dizel</option>
                <option value="LPG">LPG</option>
                <option value="Hibrit">Hibrit</option>
                <option value="Elektrik">Elektrik</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label for="transmission_type" class="form-label">Vites Tipi</label>
            <select class="form-select" id="transmission_type" name="transmission_type" required>
                <option value="Manuel">Manuel</option>
                <option value="Otomatik">Otomatik</option>
                <option value="Yarı Otomatik">Yarı Otomatik</option>
            </select>
        </div>
         <div class="col-md-4 mb-3">
            <label for="current_location_id" class="form-label">Mevcut Lokasyon</label>
            <select class="form-select" id="current_location_id" name="current_location_id" required>
                 <option value="" disabled selected>Lokasyon Seçin...</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Aracı Kaydet</button>
    <a href="/rentacar/public/admin/cars" class="btn btn-secondary">İptal</a>
</form>

<?php require_once 'includes/footer.php'; ?>