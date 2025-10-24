<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-xl-8"> <!-- Formun çok geniş olmasını engellemek için 8'lik sütun -->
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Kullanıcıyı Düzenle: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                    </div>
                    <div class="col text-right">
                        <a href="/rentacar/public/admin/users" class="btn btn-sm btn-primary">Kullanıcılara Geri Dön</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Statik Bilgiler -->
                <h6 class="heading-small text-muted mb-4">Kullanıcı Bilgileri</h6>
                <div class="pl-lg-4">
                    <div class="row">
                        <div class="col-lg-6">
                            <p class="mb-0"><strong>Kullanıcı ID:</strong> <?php echo $user['user_id']; ?></p>
                        </div>
                        <div class="col-lg-6">
                            <p class="mb-0"><strong>E-posta:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4" />

                <!-- Form -->
                <h6 class="heading-small text-muted mb-4">Yetki ve Durum</h6>
                <form action="/rentacar/public/admin/users/update" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="role">Kullanıcı Rolü</label>
                                    <!-- Temanın `form-control` sınıfını kullanıyoruz -->
                                    <select class="form-control" id="role" name="role">
                                        <option value="Customer" <?php echo ($user['role'] == 'Customer') ? 'selected' : ''; ?>>Customer (Müşteri)</option>
                                        <option value="Admin" <?php echo ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Admin (Yönetici)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="status">Hesap Durumu</label>
                                    <!-- Temanın `form-control` sınıfını kullanıyoruz -->
                                    <select class="form-control" id="status" name="status">
                                        <option value="Active" <?php echo ($user['status'] == 'Active') ? 'selected' : ''; ?>>Active (Aktif)</option>
                                        <option value="Suspended" <?php echo ($user['status'] == 'Suspended') ? 'selected' : ''; ?>>Suspended (Askıya Alınmış)</option>
                                        <option value="Pending" <?php echo ($user['status'] == 'Pending') ? 'selected' : ''; ?>>Pending (Onay Bekliyor)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
