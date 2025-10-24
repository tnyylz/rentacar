<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-xl-8"> <!-- Formun çok geniş olmasını engellemek için 8'lik sütun -->
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Lokasyonu Düzenle: <?php echo htmlspecialchars($location['location_name']); ?></h3>
                    </div>
                    <div class="col text-right">
                        <a href="/rentacar/public/admin/locations" class="btn btn-sm btn-primary">Lokasyonlara Geri Dön</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="/rentacar/public/admin/locations/update" method="POST">
                    <input type="hidden" name="location_id" value="<?php echo $location['location_id']; ?>">
                    
                    <h6 class="heading-small text-muted mb-4">Lokasyon Bilgileri</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="city">Şehir</label>
                                    <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($location['city']); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="location_name">Lokasyon Adı</label>
                                    <input type="text" id="location_name" name="location_name" class="form-control" value="<?php echo htmlspecialchars($location['location_name']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="address">Adres</label>
                                    <textarea id="address" name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($location['address']); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4" />
                    <!-- Diğer Bilgiler -->
                    <h6 class="heading-small text-muted mb-4">Diğer Bilgiler</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="phone">Telefon</label>
                                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($location['phone']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="status">Durum</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Active" <?php echo ($location['status'] == 'Active') ? 'selected' : ''; ?>>Active (Aktif)</option>
                                        <option value="Inactive" <?php echo ($location['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive (Pasif)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                                <a href="/rentacar/public/admin/locations" class="btn btn-secondary">İptal</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
