<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-xl-8"> <!-- Formun çok geniş olmasını engellemek için 8'lik sütun -->
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Yeni Kategori Ekle</h3>
                    </div>
                    <div class="col text-right">
                        <a href="/rentacar/public/admin/categories" class="btn btn-sm btn-primary">Kategorilere Geri Dön</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="/rentacar/public/admin/categories/store" method="POST">
                    
                    <h6 class="heading-small text-muted mb-4">Kategori Bilgileri</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="category_name">Kategori Adı</label>
                                    <input type="text" id="category_name" name="category_name" class="form-control" placeholder="Örn: Ekonomik" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="status">Durum</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Active" selected>Active (Aktif)</option>
                                        <option value="Inactive">Inactive (Pasif)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="description">Açıklama</label>
                                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Kategori hakkında kısa bir açıklama..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary">Kategoriyi Kaydet</button>
                                <a href="/rentacar/public/admin/categories" class="btn btn-secondary">İptal</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
