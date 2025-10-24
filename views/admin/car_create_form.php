<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Yeni Araç Ekle</h3>
                    </div>
                    <div class="col text-right">
                        <a href="/rentacar/public/admin/cars" class="btn btn-sm btn-primary">Geri Dön</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="/rentacar/public/admin/cars/store" method="POST">
                    <h6 class="heading-small text-muted mb-4">Araç Bilgileri</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="brand">Marka</label>
                                    <input type="text" id="brand" name="brand" class="form-control" placeholder="Örn: Renault" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="model">Model</label>
                                    <input type="text" id="model" name="model" class="form-control" placeholder="Örn: Clio" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="year">Yıl</label>
                                    <input type="number" id="year" name="year" class="form-control" min="1990" max="<?php echo date('Y') + 1; ?>" placeholder="Örn: 2023" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="license_plate">Plaka</label>
                                    <input type="text" id="license_plate" name="license_plate" class="form-control" placeholder="Örn: 35 ABC 123" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4" />
                    <h6 class="heading-small text-muted mb-4">Teknik Özellikler ve Fiyat</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="daily_rate">Günlük Ücret (TL)</label>
                                    <input type="number" id="daily_rate" name="daily_rate" class="form-control" step="0.01" placeholder="Örn: 850.00" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="category_id">Kategori</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="" disabled selected>Kategori Seçin...</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="fuel_type">Yakıt Tipi</label>
                                    <select class="form-control" id="fuel_type" name="fuel_type" required>
                                        <option>Benzin</option><option>Dizel</option><option>LPG</option><option>Hibrit</option><option>Elektrik</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="transmission_type">Vites Tipi</label>
                                    <select class="form-control" id="transmission_type" name="transmission_type" required>
                                        <option>Manuel</option><option>Otomatik</option><option>Yarı Otomatik</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                     <hr class="my-4" />
                    <h6 class="heading-small text-muted mb-4">Lokasyon Bilgisi</h6>
                     <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                     <label class="form-control-label" for="current_location_id">Mevcut Lokasyon</label>
                                    <select class="form-control" id="current_location_id" name="current_location_id" required>
                                        <option value="" disabled selected>Lokasyon Seçin...</option>
                                        <?php foreach ($locations as $location): ?>
                                            <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                     </div>
                     <div class="pl-lg-4 mt-4">
                        <button type="submit" class="btn btn-primary">Aracı Kaydet</button>
                     </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
