<?php require_once 'includes/header.php'; ?>

<h1 class="h2">Kategoriyi Düzenle: <?php echo htmlspecialchars($category['category_name']); ?></h1>

<form action="/rentacar/public/admin/categories/update" method="POST" class="mt-4">
    <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">

    <div class="mb-3">
        <label for="category_name" class="form-label">Kategori Adı</label>
        <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Açıklama</label>
        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Durum</label>
        <select class="form-select" id="status" name="status">
            <option value="Active" <?php echo ($category['status'] == 'Active') ? 'selected' : ''; ?>>Active (Aktif)</option>
            <option value="Inactive" <?php echo ($category['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive (Pasif)</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
    <a href="/rentacar/public/admin/categories" class="btn btn-secondary">İptal</a>
</form>

<?php require_once 'includes/footer.php'; ?>