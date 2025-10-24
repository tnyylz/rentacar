<?php require_once 'includes/header.php'; ?>

<!-- Sayfa İçeriği -->
<div class="row">
    <div class="col">
        <div class="card">
            <!-- Kart Başlığı ve Yeni Ekle Butonu -->
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Araç Yönetimi</h3>
                <a href="/rentacar/public/admin/cars/create" class="btn btn-sm btn-primary">Yeni Araç Ekle</a>
            </div>
            <!-- Argon Temalı Tablo -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Araç</th>
                            <th scope="col">Plaka</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Günlük Ücret</th>
                            <th scope="col">Durum</th>
                            <th scope="col" class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <?php if (isset($cars) && !empty($cars)): ?>
                            <?php foreach ($cars as $car): ?>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <!-- İsterseniz buraya araç resmi de ekleyebilirsiniz -->
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm font-weight-bold"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></span>
                                                <span class="d-block text-muted text-xs"><?php echo $car['year']; ?> Model</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <?php echo htmlspecialchars($car['license_plate']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($car['category_name'] ?? 'Belirtilmemiş'); ?>
                                    </td>
                                    <td class="budget">
                                        <?php echo number_format($car['daily_rate'], 2, ',', '.'); ?> TL
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <?php
                                                // Duruma göre renk sınıfını belirle
                                                $status_class = 'bg-default';
                                                switch ($car['status']) {
                                                    case 'Müsait': $status_class = 'bg-success'; break;
                                                    case 'Kiralandı': $status_class = 'bg-warning'; break;
                                                    case 'Bakımda': $status_class = 'bg-danger'; break;
                                                }
                                            ?>
                                            <i class="<?php echo $status_class; ?>"></i>
                                            <span class="status"><?php echo htmlspecialchars($car['status']); ?></span>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="/rentacar/public/admin/cars/edit?id=<?php echo $car['car_id']; ?>">Düzenle</a>
                                                <?php if ($_SESSION['user_id'] != $car['car_id']): ?>
                                                    <a class="dropdown-item text-danger" href="/rentacar/public/admin/cars/delete?id=<?php echo $car['car_id']; ?>" onclick="return confirm('Emin misiniz?');">Sil</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted p-5">Sistemde kayıtlı araç bulunmuyor.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Kart Alt Bilgisi - Sayfalama -->
            <div class="card-footer py-4">
                <?php 
                if (isset($total_pages) && isset($current_page)) {
                    // Sayfalama linklerini temanın kendi stiliyle uyumlu hale getireceğiz.
                    // Şimdilik eski pagination.php'yi çağırıyoruz.
                    require_once 'includes/pagination.php'; 
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
