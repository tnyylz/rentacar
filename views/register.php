<?php require_once 'includes/header.php'; ?>

<?php
$old_input = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Kayıt Ol</h2>
        <form action="/rentacar/public/register-submit" method="post">
            <div class="mb-3">
                <label for="first_name" class="form-label">Ad:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($old_input['first_name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Soyad:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($old_input['last_name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-posta:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($old_input['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifre:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Şifre Tekrar:</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn btn-primary">Kayıt Ol</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>