<?php require_once 'includes/header.php'; ?>

<!-- Sayfa İçeriği -->
<div class="row">
    <div class="col">
        <div class="card">
            <!-- Kart Başlığı -->
            <div class="card-header border-0">
                <h3 class="mb-0">Rezervasyon Yönetimi</h3>
            </div>
            <!-- Argon Temalı Tablo -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Müşteri</th>
                            <th scope="col">Araç</th>
                            <th scope="col">Tarih Aralığı</th>
                            <th scope="col">Tutar</th>
                            <th scope="col">Durum</th>
                            <th scope="col" class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <?php if (isset($reservations) && !empty($reservations)): ?>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm font-weight-bold"><?php echo htmlspecialchars($reservation['user_full_name']); ?></span>
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <?php echo htmlspecialchars($reservation['car_name']); ?>
                                        <span class="d-block text-muted text-xs"><?php echo htmlspecialchars($reservation['license_plate']); ?></span>
                                    </td>
                                    <td>
                                        <span class="d-block text-sm"><b>Alış:</b> <?php echo date('d.m.Y H:i', strtotime($reservation['start_date'])); ?></span>
                                        <span class="d-block text-sm"><b>Teslim:</b> <?php echo date('d.m.Y H:i', strtotime($reservation['end_date'])); ?></span>
                                    </td>
                                    <td class="budget">
                                        <?php echo number_format($reservation['total_price'], 2, ',', '.'); ?> TL
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <?php
                                                $status_class = 'bg-default';
                                                switch ($reservation['status']) {
                                                    case 'Onaylandı': $status_class = 'bg-success'; break;
                                                    case 'Beklemede': $status_class = 'bg-warning'; break;
                                                    case 'İptal Edildi': $status_class = 'bg-danger'; break;
                                                    case 'Tamamlandı': $status_class = 'bg-info'; break;
                                                }
                                            ?>
                                            <i class="<?php echo $status_class; ?>"></i>
                                            <span class="status"><?php echo htmlspecialchars($reservation['status']); ?></span>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="/rentacar/public/admin/reservations/edit?id=<?php echo $reservation['reservation_id']; ?>">
                                                    <i class="ni ni-settings text-info"></i> Detay / Düzenle
                                                </a>
                                                <a class="dropdown-item text-danger" href="/rentacar/public/admin/reservations/delete?id=<?php echo $reservation['reservation_id']; ?>" 
                                                   onclick="return confirm('Bu rezervasyonu kalıcı olarak silmek istediğinizden emin misiniz?');">
                                                   <i class="ni ni-fat-remove"></i> Sil
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted p-5">Sistemde kayıtlı rezervasyon bulunmuyor.</td>
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