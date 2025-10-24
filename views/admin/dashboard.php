<?php 
// Admin paneli için oluşturduğumuz header'ı çağır
require_once 'includes/header.php'; 
?>

<!-- ========================================================= -->
<!-- SAYFANIN İÇERİĞİ BURADA BAŞLIYOR (header.php'den sonra) -->
<!-- ========================================================= -->

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats">
            <!-- Card body -->
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Toplam Araç</h5>
                        <span class="h2 font-weight-bold mb-0"><?php echo $total_cars ?? 0; ?></span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                            <i class="ni ni-delivery-fast"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-nowrap">Sistemdeki toplam araç sayısı.</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats">
            <!-- Card body -->
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Bekleyen Rezervasyon</h5>
                        <span class="h2 font-weight-bold mb-0"><?php echo $pending_reservations ?? 0; ?></span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                            <i class="ni ni-calendar-grid-58"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-nowrap">Onay bekleyen yeni rezervasyonlar.</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats">
            <!-- Card body -->
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Toplam Müşteri</h5>
                        <span class="h2 font-weight-bold mb-0"><?php echo $total_customers ?? 0; ?></span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                            <i class="ni ni-single-02"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-nowrap">Sisteme kayıtlı toplam müşteri.</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats">
            <!-- Card body -->
            <div class="card-body">
                 <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Genel Memnuniyet</h5>
                        <span class="h2 font-weight-bold mb-0">★ <?php echo number_format($overall_avg_rating ?? 0, 2); ?></span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                            <i class="ni ni-like-2"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-nowrap">Tüm araçların puan ortalaması.</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- SAYFANIN İÇERİĞİ BURADA BİTİYOR (footer.php'den önce) -->
<!-- ========================================================= -->
<?php 
// Admin paneli için oluşturduğumuz footer'ı çağır
?>

<div class="row mt-4">
    <div class="col">
        <div class="card">
            <!-- Kart Başlığı -->
            <div class="card-header border-0">
                <h3 class="mb-0">Son Rezervasyonlar</h3>
            </div>
            <!-- Argon Temalı Tablo -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Araç</th>
                            <th scope="col">Müşteri</th>
                            <th scope="col">Tutar</th>
                            <th scope="col">Durum</th>
                            <th scope="col"></th> <!-- İşlemler butonu için boş başlık -->
                        </tr>
                    </thead>
                    <tbody class="list">
                        <?php if (isset($recent_reservations) && !empty($recent_reservations)): ?>
                            <?php foreach ($recent_reservations as $reservation): ?>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm"><?php echo htmlspecialchars($reservation['car_name']); ?></span>
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <?php echo htmlspecialchars($reservation['user_full_name']); ?>
                                    </td>
                                    <td class="budget">
                                        <?php echo number_format($reservation['total_price'], 2, ',', '.'); ?> TL
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <?php
                                                // Rezervasyon durumuna göre renk belirle
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
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="/rentacar/public/admin/reservations/edit?id=<?php echo $reservation['reservation_id']; ?>">Detay/Düzenle</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">Gösterilecek yeni rezervasyon bulunmuyor.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?>
 