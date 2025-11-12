<?php require_once 'includes/header.php'; ?>

<h1 class="mb-4 my-3">Profilim</h1>

<div class="row">
    <!-- Sol Sütun: Mevcut Resim -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Profil Resmim</h5>
            </div>
            <div class="card-body text-center">
                <?php 
                  // Controller'dan gelen $user dizisini kullan
                  $profile_pic = $user['profile_image_url'] ?? null;
                  if ($profile_pic): 
                ?>
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profil Resmi" class="img-fluid rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                <?php else: ?>
                    <!-- Resim yoksa, ui-avatars.com'dan varsayılanı oluştur -->
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['first_name'] . ' ' . $user['last_name']); ?>&background=0D6EFD&color=fff&size=200&rounded=true" alt="Varsayılan Avatar" class="img-fluid rounded-circle">
                <?php endif; ?>
                
                <h5 class="mt-3 mb-0"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>

    <!-- Sağ Sütun: Güncelleme Formu -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Profili Düzenle</h5>
            </div>
            <div class="card-body">
                <!-- 
                  Formun 'action'u '/rentacar/public/update-profile' olmalı 
                  ve dosya yükleme için 'enctype' içermeli.
                -->
                <form action="/rentacar/public/profile/update" method="POST" enctype="multipart/form-data">
                    <h6 class="text-muted mb-3">Kullanıcı Bilgileri</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Ad</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Soyad</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta Adresi</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profil Resmini Değiştir</label>
                        <input type="file" name="profile_image" id="profile_image" class="form-control">
                        <small class="form-text text-muted">Sadece .jpg, .jpeg, .png formatları. En fazla 2MB. Boş bırakırsanız mevcut resim korunur.</small>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-muted mb-3">Şifre Değiştir</h6>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mevcut Şifreniz</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">Yeni Şifre</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" autocomplete="new-password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_new_password" class="form-label">Yeni Şifre (Tekrar)</label>
                            <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                    <small class="form-text text-muted d-block mb-3">Şifrenizi değiştirmek istemiyorsanız şifre alanlarını boş bırakın.</small>

                    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

