<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kategori Yönetimi</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/rentacar/public/admin/categories/create" class="btn btn-sm btn-outline-primary">
            + Yeni Kategori Ekle
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#ID</th>
                <th scope="col">Kategori Adı</th>
                <th scope="col">Açıklama</th>
                <th scope="col">Durum</th>
                <th scope="col">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo $category['category_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($category['category_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($category['description']); ?></td>
                    <td>
                        <span class="badge <?php echo ($category['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo htmlspecialchars($category['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="/rentacar/public/admin/categories/edit?id=<?php echo $category['category_id']; ?>" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                        <a href="/rentacar/public/admin/categories/delete?id=<?php echo $category['category_id']; ?>" onclick="return confirm('Bu kategoriyi kalıcı olarak silmek istediğinizden emin misiniz?');" class="btn btn-sm btn-outline-danger">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>