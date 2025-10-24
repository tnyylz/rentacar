<?php require_once 'includes/header.php'; ?>

<!-- Sayfa İçeriği -->
<div class="row">
    <div class="col">
        <div class="card">
            <!-- Kart Başlığı ve Yeni Ekle Butonu -->
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Kategori Yönetimi</h3>
                <a href="/rentacar/public/admin/categories/create" class="btn btn-sm btn-primary">Yeni Kategori Ekle</a>
            </div>
            <!-- Argon Temalı Tablo -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Kategori Adı</th>
                            <th scope="col">Açıklama</th>
                            <th scope="col">Durum</th>
                            <th scope="col" class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td class="text-sm font-weight-bold">
                                        <?php echo $category['category_id']; ?>
                                    </td>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm font-weight-bold"><?php echo htmlspecialchars($category['category_name']); ?></span>
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <span class="d-block text-sm"><?php echo htmlspecialchars(mb_strimwidth($category['description'], 0, 70, "...")); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <?php
                                                $status_class = ($category['status'] == 'Active') ? 'bg-success' : 'bg-secondary';
                                            ?>
                                            <i class="<?php echo $status_class; ?>"></i>
                                            <span class="status"><?php echo htmlspecialchars($category['status']); ?></span>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="/rentacar/public/admin/categories/edit?id=<?php echo $category['category_id']; ?>">
                                                    <i class="ni ni-settings text-info"></i> Düzenle
                                                </a>
                                                <a class="dropdown-item text-danger" href="/rentacar/public/admin/categories/delete?id=<?php echo $category['category_id']; ?>" 
                                                   onclick="return confirm('Bu kategoriyi kalıcı olarak silmek istediğinizden emin misiniz?');">
                                                   <i class="ni ni-fat-remove"></i> Sil
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted p-5">Sistemde kayıtlı kategori bulunmuyor.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Kart Alt Bilgisi - Sayfalama -->
            <div class="card-footer py-4">
                <?php 
                if (isset($total_pages) && isset($current_page)) {
                    require_once 'includes/pagination.php'; 
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
