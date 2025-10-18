<?php require_once 'includes/header.php'; ?>

<h1 class="h2">Kullanıcıyı Düzenle: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>

<div class="card mt-4">
    <div class="card-header">
        Kullanıcı Bilgileri
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><strong>#ID:</strong> <?php echo $user['user_id']; ?></li>
        <li class="list-group-item"><strong>E-posta:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
    </ul>
</div>

<form action="/rentacar/public/admin/users/update" method="POST" class="mt-4">
    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="role" class="form-label"><strong>Kullanıcı Rolü</strong></label>
            <select class="form-select" id="role" name="role">
                <option value="Customer" <?php echo ($user['role'] == 'Customer') ? 'selected' : ''; ?>>Customer (Müşteri)</option>
                <option value="Admin" <?php echo ($user['role'] == 'Admin') ? 'selected' : ''; ?>>Admin (Yönetici)</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="status" class="form-label"><strong>Hesap Durumu</strong></label>
            <select class="form-select" id="status" name="status">
                <option value="Active" <?php echo ($user['status'] == 'Active') ? 'selected' : ''; ?>>Active (Aktif)</option>
                <option value="Suspended" <?php echo ($user['status'] == 'Suspended') ? 'selected' : ''; ?>>Suspended (Askıya Alınmış)</option>
                <option value="Pending" <?php echo ($user['status'] == 'Pending') ? 'selected' : ''; ?>>Pending (Onay Bekliyor)</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
    <a href="/rentacar/public/admin/users" class="btn btn-secondary">Geri Dön</a>
</form>

<?php require_once 'includes/footer.php'; ?>