<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Aracı Düzenle: <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                    </div>
                    <div class="col text-right">
                        <a href="/rentacar/public/admin/cars" class="btn btn-sm btn-primary">Geri Dön</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="/rentacar/public/admin/cars/update" method="POST">
                    <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                    <h6 class="heading-small text-muted mb-4">Araç Bilgileri</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="brand">Marka</label>
                                    <input type="text" id="brand" name="brand" class="form-control" value="<?php echo htmlspecialchars($car['brand']); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="model">Model</label>
                                    <input type="text" id="model" name="model" class="form-control" value="<?php echo htmlspecialchars($car['model']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="year">Yıl</label>
                                    <input type="number" id="year" name="year" class="form-control" value="<?php echo htmlspecialchars($car['year']); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="license_plate">Plaka</label>
                                    <input type="text" id="license_plate" name="license_plate" class="form-control" value="<?php echo htmlspecialchars($car['license_plate']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4" />
                    <h6 class="heading-small text-muted mb-4">Teknik Özellikler ve Fiyat</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                             <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="category_id">Kategori</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['category_id']; ?>" <?php echo ($car['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="fuel_type">Yakıt Tipi</label>
                                    <select class="form-control" id="fuel_type" name="fuel_type" required>
                                        <option <?php echo ($car['fuel_type'] == 'Benzin') ? 'selected' : ''; ?>>Benzin</option>
                                        <option <?php echo ($car['fuel_type'] == 'Dizel') ? 'selected' : ''; ?>>Dizel</option>
                                        <option <?php echo ($car['fuel_type'] == 'LPG') ? 'selected' : ''; ?>>LPG</option>
                                        <option <?php echo ($car['fuel_type'] == 'Hibrit') ? 'selected' : ''; ?>>Hibrit</option>
                                        <option <?php echo ($car['fuel_type'] == 'Elektrik') ? 'selected' : ''; ?>>Elektrik</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="transmission_type">Vites Tipi</label>
                                    <select class="form-control" id="transmission_type" name="transmission_type" required>
                                        <option <?php echo ($car['transmission_type'] == 'Manuel') ? 'selected' : ''; ?>>Manuel</option>
                                        <option <?php echo ($car['transmission_type'] == 'Otomatik') ? 'selected' : ''; ?>>Otomatik</option>
                                        <option <?php echo ($car['transmission_type'] == 'Yarı Otomatik') ? 'selected' : ''; ?>>Yarı Otomatik</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="daily_rate">Günlük Ücret (TL)</label>
                                    <input type="number" id="daily_rate" name="daily_rate" class="form-control" step="0.01" value="<?php echo htmlspecialchars($car['daily_rate']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                     <hr class="my-4" />
                    <h6 class="heading-small text-muted mb-4">Lokasyon ve Durum</h6>
                     <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                     <label class="form-control-label" for="current_location_id">Mevcut Lokasyon</label>
                                    <select class="form-control" id="current_location_id" name="current_location_id" required>
                                        <?php foreach ($locations as $location): ?>
                                            <option value="<?php echo $location['location_id']; ?>" <?php echo ($car['current_location_id'] == $location['location_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($location['location_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="status">Durum</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="Müsait" <?php echo ($car['status'] == 'Müsait') ? 'selected' : ''; ?>>Müsait</option>
                                        <option value="Kiralandı" <?php echo ($car['status'] == 'Kiralandı') ? 'selected' : ''; ?>>Kiralandı</option>
                                        <option value="Bakımda" <?php echo ($car['status'] == 'Bakımda') ? 'selected' : ''; ?>>Bakımda</option>
                                        <option value="Pasif" <?php echo ($car['status'] == 'Pasif') ? 'selected' : ''; ?>>Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                     </div>
                     <div class="pl-lg-4 mt-4">
                        <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                     </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
