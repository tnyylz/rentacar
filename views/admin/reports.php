<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Raporlar</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <h3>En Çok Kiralanan 5 Araç</h3>
        <?php if (!empty($top_cars)): ?>
            <ul class="list-group">
                <?php foreach ($top_cars as $car): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($car['car_name']); ?>
                        <span class="badge bg-primary rounded-pill"><?php echo $car['rental_count']; ?> kez</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-info">Henüz yeterli rezervasyon verisi yok.</div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <h3>Aylık Kazançlar (Tamamlanan Kiralamalar)</h3>
        <?php if (!empty($monthly_revenue)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ay</th>
                        <th>Toplam Kazanç</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthly_revenue as $revenue): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($revenue['month']); ?></td>
                            <td><strong><?php echo htmlspecialchars(number_format($revenue['total_revenue'], 2, ',', '.')); ?> TL</strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Henüz tamamlanmış bir kiralama yok.</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>