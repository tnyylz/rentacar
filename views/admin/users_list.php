<?php require_once 'includes/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kullanıcı Yönetimi</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#ID</th>
                <th scope="col">Ad Soyad</th>
                <th scope="col">E-posta</th>
                <th scope="col">Rol</th>
                <th scope="col">Kayıt Tarihi</th>
                <th scope="col">Durum</th>
                <th scope="col">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="badge <?php echo ($user['role'] == 'Admin') ? 'bg-primary' : 'bg-info'; ?>">
                            <?php echo htmlspecialchars($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <span class="badge <?php echo ($user['status'] == 'Active') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                            <?php echo htmlspecialchars($user['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="/rentacar/public/admin/users/edit?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>