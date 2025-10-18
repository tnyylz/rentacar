<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Ana Sayfa (Dashboard)</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Toplam Araç Sayısı</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $total_cars; ?> Araç</h5>
                <p class="card-text">Sistemde kayıtlı toplam araç sayısı.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Bekleyen Rezervasyonlar</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $pending_reservations; ?> Rezervasyon</h5>
                <p class="card-text">Onay bekleyen yeni rezervasyonlar.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Toplam Müşteri</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $total_customers; ?> Müşteri</h5>
                <p class="card-text">Sisteme kayıtlı toplam müşteri sayısı.</p>
            </div>
        </div>
    </div>
</div>

<h2 class="mt-4">Son Rezervasyonlar</h2>
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Müşteri</th>
                <th>Araç</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_reservations as $reservation): ?>
                <tr>
                    <td><?php echo $reservation['reservation_id']; ?></td>
                    <td><?php echo htmlspecialchars($reservation['user_full_name']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['car_name']); ?></td>
                    <td>
                         <span class="badge 
                            <?php 
                                switch ($reservation['status']) {
                                    case 'Onaylandı': echo 'bg-success'; break;
                                    case 'Tamamlandı': echo 'bg-secondary'; break;
                                    case 'İptal Edildi': echo 'bg-danger'; break;
                                    case 'Beklemede': echo 'bg-warning text-dark'; break;
                                    default: echo 'bg-info';
                                }
                            ?>">
                            <?php echo htmlspecialchars($reservation['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<?php require_once 'includes/footer.php'; ?>