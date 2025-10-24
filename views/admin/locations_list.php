<?php require_once 'includes/header.php'; ?>

<!-- Sayfa İçeriği -->
<div class="row">
    <div class="col">
        <div class="card">
            <!-- Kart Başlığı ve Yeni Ekle Butonu -->
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Lokasyon Yönetimi</h3>
                <a href="/rentacar/public/admin/locations/create" class="btn btn-sm btn-primary">Yeni Lokasyon Ekle</a>
            </div>
            <!-- Argon Temalı Tablo -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Lokasyon Adı</th>
                            <th scope="col">Şehir</th>
                            <th scope="col">Telefon</th>
                            <th scope="col">Durum</th>
                            <th scope="col" class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <?php if (isset($locations) && !empty($locations)): ?>
                            <?php foreach ($locations as $location): ?>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm font-weight-bold"><?php echo htmlspecialchars($location['location_name']); ?></span>
                                                <!-- Adresi, lokasyon adının altında küçük olarak gösterebiliriz -->
                                                <span class="d-block text-muted text-xs"><?php echo htmlspecialchars(mb_strimwidth($location['address'], 0, 40, "...")); ?></span>
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <?php echo htmlspecialchars($location['city']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($location['phone']); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <?php
                                                $status_class = ($location['status'] == 'Active') ? 'bg-success' : 'bg-secondary';
                                            ?>
                                            <i class="<?php echo $status_class; ?>"></i>
                                            <span class="status"><?php echo htmlspecialchars($location['status']); ?></span>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="/rentacar/public/admin/locations/edit?id=<?php echo $location['location_id']; ?>">
                                                    <i class="ni ni-settings text-info"></i> Düzenle
                                                </a>
                                                <a class="dropdown-item text-danger" href="/rentacar/public/admin/locations/delete?id=<?php echo $location['location_id']; ?>" 
                                                   onclick="return confirm('Bu lokasyonu kalıcı olarak silmek istediğinizden emin misiniz?');">
                                                   <i class="ni ni-fat-remove"></i> Sil
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted p-5">Sistemde kayıtlı lokasyon bulunmuyor.</td>
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
