<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-xl-8"> <!-- Formun çok geniş olmasını engellemek için 8'lik sütun -->
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Yeni Lokasyon Ekle</h3>
                    </div>
                    <div class="col text-right">
                        <a href="/rentacar/public/admin/locations" class="btn btn-sm btn-primary">Lokasyonlara Geri Dön</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="/rentacar/public/admin/locations/store" method="POST">
                    
                    <h6 class="heading-small text-muted mb-4">Lokasyon Bilgileri</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="city">Şehir</label>
                                    <input type="text" id="city" name="city" class="form-control" placeholder="Örn: İzmir" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="location_name">Lokasyon Adı</label>
                                    <input type="text" id="location_name" name="location_name" class="form-control" placeholder="Örn: Havalimanı Ofis" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="address">Adres</label>
                                    <textarea id="address" name="address" class="form-control" rows="3" placeholder="Detaylı adres..." required></textarea>
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
                                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Örn: 0232 555 1212">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="status">Durum</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Active" selected>Active (Aktif)</option>
                                        <option value="Inactive">Inactive (Pasif)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary">Lokasyonu Kaydet</button>
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
