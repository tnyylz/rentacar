<?php require_once 'includes/header.php'; ?>

<h1 class="h2">Yeni Kategori Ekle</h1>

<form action="/rentacar/public/admin/categories/store" method="POST" class="mt-4">
    <div class="mb-3">
        <label for="category_name" class="form-label">Kategori Adı</label>
        <input type="text" class="form-control" id="category_name" name="category_name" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Açıklama</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Durum</label>
        <select class="form-select" id="status" name="status">
            <option value="Active" selected>Active (Aktif)</option>
            <option value="Inactive">Inactive (Pasif)</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Kategoriyi Kaydet</button>
    <a href="/rentacar/public/admin/categories" class="btn btn-secondary">İptal</a>
</form>

<?php require_once 'includes/footer.php'; ?>