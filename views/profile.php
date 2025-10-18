<?php require_once 'includes/header.php'; ?>

<h1 class="mb-4">Profilim</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Şifre Değiştir
            </div>
            <div class="card-body">
                <form action="/rentacar/public/update-password" method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mevcut Şifreniz</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Yeni Şifre</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">Yeni Şifre (Tekrar)</label>
                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        </div>
</div>

<?php require_once 'includes/footer.php'; ?>