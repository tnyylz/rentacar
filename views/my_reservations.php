<?php require_once 'includes/header.php'; ?>

<h1 class="mb-4 my-3">Rezervasyonlarım</h1>

<h2 class="h4">Aktif ve Gelecek Rezervasyonlar</h2>
<?php if (!empty($active_reservations)): ?>
    <div class="table-responsive mb-5">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Araç</th>
                    <th scope="col">Alış Tarihi</th>
                    <th scope="col">Teslim Tarihi</th>
                    <th scope="col">Toplam Fiyat</th>
                    <th scope="col">Durum</th>
                    <th scope="col">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($active_reservations as $reservation): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($reservation['brand'] . ' ' . $reservation['model']); ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['start_date'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['end_date'])); ?></td>
                        <td><?php echo htmlspecialchars($reservation['total_price']); ?> TL</td>
                        <td>
                            <span class="badge bg-success"><?php echo htmlspecialchars($reservation['status']); ?></span>
                        </td>
                        <td>
                            <a href="/rentacar/public/cancel-reservation?id=<?php echo $reservation['reservation_id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Bu rezervasyonu iptal etmek istediğinizden emin misiniz?');">
                               İptal Et
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info" role="alert">
        Aktif veya gelecek bir rezervasyonunuz bulunmamaktadır.
    </div>
<?php endif; ?>


<h2 class="h4 mt-5">Geçmiş Rezervasyonlar</h2>
<?php if (!empty($past_reservations)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-secondary">
                <tr>
                    <th scope="col">Araç</th>
                    <th scope="col">Alış Tarihi</th>
                    <th scope="col">Teslim Tarihi</th>
                    <th scope="col">Toplam Fiyat</th>
                    <th scope="col">Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($past_reservations as $reservation): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($reservation['brand'] . ' ' . $reservation['model']); ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['start_date'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['end_date'])); ?></td>
                        <td><?php echo htmlspecialchars($reservation['total_price']); ?> TL</td>
                        <td>
                            <span class="badge <?php echo ($reservation['status'] == 'İptal Edildi') ? 'bg-danger' : 'bg-secondary'; ?>">
                                <?php echo htmlspecialchars($reservation['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-light" role="alert">
        Henüz tamamlanmış veya iptal edilmiş bir rezervasyonunuz bulunmamaktadır.
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>