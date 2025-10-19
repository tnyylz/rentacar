<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Araç Yönetimi</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/rentacar/public/admin/cars/create" class="btn btn-sm btn-outline-primary">
            + Yeni Araç Ekle
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#ID</th>
                <th scope="col">Marka / Model</th>
                <th scope="col">Plaka</th>
                <th scope="col">Kategori</th>
                <th scope="col">Günlük Ücret</th>
                <th scope="col">Durum</th>
                <th scope="col">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cars as $car): ?>
                <tr>
                    <td><?php echo $car['car_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></strong> (<?php echo $car['year']; ?>)</td>
                    <td><?php echo htmlspecialchars($car['license_plate']); ?></td>
                    <td><?php echo htmlspecialchars($car['category_name'] ?? 'Belirtilmemiş'); ?></td>
                    <td><?php echo htmlspecialchars($car['daily_rate']); ?> TL</td>
                    <td>
                        <span class="badge 
                            <?php 
                                switch ($car['status']) {
                                    case 'Müsait': echo 'bg-success'; break;
                                    case 'Kiralandı': echo 'bg-warning'; break;
                                    case 'Bakımda': echo 'bg-danger'; break;
                                    default: echo 'bg-secondary';
                                }
                            ?>">
                            <?php echo htmlspecialchars($car['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="/rentacar/public/admin/cars/edit?id=<?php echo $car['car_id']; ?>" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                        <a href="/rentacar/public/admin/cars/delete?id=<?php echo $car['car_id']; ?>" 
                        class="btn btn-sm btn-outline-danger" 
                        onclick="return confirm('Bu aracı kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.');">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>