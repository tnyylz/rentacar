<?php require_once 'includes/header.php'; ?>

<h1 class="h2">Yeni Lokasyon Ekle</h1>

<form action="/rentacar/public/admin/locations/store" method="POST" class="mt-4">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="city" class="form-label">Şehir</label>
            <input type="text" class="form-control" id="city" name="city" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="location_name" class="form-label">Lokasyon Adı</label>
            <input type="text" class="form-control" id="location_name" name="location_name" required>
        </div>
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Adres</label>
        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">Telefon</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <div class="col-md-6 mb-3">
            <label for="status" class="form-label">Durum</label>
            <select class="form-select" id="status" name="status">
                <option value="Active" selected>Active (Aktif)</option>
                <option value="Inactive">Inactive (Pasif)</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Lokasyonu Kaydet</button>
    <a href="/rentacar/public/admin/locations" class="btn btn-secondary">İptal</a>
</form>

<?php require_once 'includes/footer.php'; ?>