<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Rezervasyon Yönetimi</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#ID</th>
                <th scope="col">Müşteri</th>
                <th scope="col">Araç</th>
                <th scope="col">Alış Tarihi</th>
                <th scope="col">Teslim Tarihi</th>
                <th scope="col">Tutar</th>
                <th scope="col">Durum</th>
                <th scope="col">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?php echo $reservation['reservation_id']; ?></td>
                    <td><?php echo htmlspecialchars($reservation['user_full_name']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['car_name']); ?> (<?php echo htmlspecialchars($reservation['license_plate']); ?>)</td>
                    <td><?php echo date('d.m.Y H:i', strtotime($reservation['start_date'])); ?></td>
                    <td><?php echo date('d.m.Y H:i', strtotime($reservation['end_date'])); ?></td>
                    <td><?php echo htmlspecialchars($reservation['total_price']); ?> TL</td>
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
                    <td>
                        <a href="/rentacar/public/admin/reservations/edit?id=<?php echo $reservation['reservation_id']; ?>" class="btn btn-sm btn-outline-secondary">Detay/Düzenle</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>