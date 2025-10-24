<?php require_once 'includes/header.php'; ?>

<!-- Sayfa İçeriği -->
<div class="row">
    <div class="col">
        <div class="card">
            <!-- Kart Başlığı -->
            <div class="card-header border-0">
                <h3 class="mb-0">Kullanıcı Yönetimi</h3>
            </div>
            <!-- Argon Temalı Tablo -->
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Müşteri</th>
                            <th scope="col">E-posta</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Durum</th>
                            <th scope="col">Kayıt Tarihi</th>
                            <th scope="col" class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <?php if (isset($users) && !empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <th scope="row">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="name mb-0 text-sm font-weight-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                                            </div>
                                        </div>
                                    </th>
                                    <td>
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill <?php echo ($user['role'] == 'Admin') ? 'badge-primary' : 'badge-info'; ?>">
                                            <?php echo htmlspecialchars($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <?php
                                                $status_class = 'bg-default';
                                                switch ($user['status']) {
                                                    case 'Active': $status_class = 'bg-success'; break;
                                                    case 'Suspended': $status_class = 'bg-warning'; break;
                                                    case 'Pending': $status_class = 'bg-danger'; break;
                                                }
                                            ?>
                                            <i class="<?php echo $status_class; ?>"></i>
                                            <span class="status"><?php echo htmlspecialchars($user['status']); ?></span>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('d.m.Y', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="/rentacar/public/admin/users/edit?id=<?php echo $user['user_id']; ?>">
                                                    <i class="ni ni-settings text-info"></i> Düzenle
                                                </a>
                                                <?php if ($_SESSION['user_id'] != $user['user_id']): // Adminin kendini silmesini engelle ?>
                                                    <a class="dropdown-item text-danger" href="/rentacar/public/admin/users/delete?id=<?php echo $user['user_id']; ?>" 
                                                       onclick="return confirm('Bu kullanıcıyı kalıcı olarak silmek istediğinizden emin misiniz?');">
                                                       <i class="ni ni-fat-remove"></i> Sil
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted p-5">Sistemde kayıtlı kullanıcı bulunmuyor.</td>
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
