<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Lokasyon Yönetimi</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/rentacar/public/admin/locations/create" class="btn btn-sm btn-outline-primary">
            + Yeni Lokasyon Ekle
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#ID</th>
                <th scope="col">Şehir</th>
                <th scope="col">Lokasyon Adı</th>
                <th scope="col">Adres</th>
                <th scope="col">Telefon</th>
                <th scope="col">Durum</th>
                <th scope="col">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($locations as $location): ?>
                <tr>
                    <td><?php echo $location['location_id']; ?></td>
                    <td><?php echo htmlspecialchars($location['city']); ?></td>
                    <td><strong><?php echo htmlspecialchars($location['location_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($location['address']); ?></td>
                    <td><?php echo htmlspecialchars($location['phone']); ?></td>
                    <td>
                        <span class="badge <?php echo ($location['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo htmlspecialchars($location['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="/rentacar/public/admin/locations/edit?id=<?php echo $location['location_id']; ?>" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                        <a href="/rentacar/public/admin/locations/delete?id=<?php echo $location['location_id']; ?>"onclick="return confirm('Bu Lokasyonu kalıcı olarak silmek istediğinizden emin misiniz?');" class="btn btn-sm btn-outline-danger">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>