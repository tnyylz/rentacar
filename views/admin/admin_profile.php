<?php 
require_once 'includes/header.php'; 
?>



<!-- Sayfa İçeriği -->
<div class="container-fluid mt--6">
    <div class="row">
        <!-- Sağ Sütun: Mevcut Resim -->
        <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
            <div class="card">
                <div class="row">
                    <div class="col">
                        <div class="card-profile-image row mb-4" style="img-rounded">
                            <a href="#">
                                <?php 
                                  $profile_pic = $_SESSION['profile_image_url'] ?? null;
                                  if ($profile_pic): 
                                ?>
                                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profil Resmi" class="rounded-circle" style="object-fit: cover;">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['first_name'] . ' ' . $user['last_name']); ?>&background=5E72E4&color=fff&size=180&rounded=true" alt="Varsayılan Avatar" class="rounded-circle">
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center">
                        <h5 class="h3 mt-5">
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?><span class="font-weight-light"> (Yönetici)</span>
                            </h5>
                            <div class="h5 font-weight-300">
                                <i class="ni ni-email-83 mr-2"></i><?php echo htmlspecialchars($user['email']); ?>
                            </div>
                    </div>
                </div>
            </div>


           
        </div>

        <!-- Sol Sütun: Güncelleme Formu -->
        <div class="col-xl-8 order-xl-1">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Profili Düzenle</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="/rentacar/public/admin/profile/update" method="POST" enctype="multipart/form-data">
                        <h6 class="heading-small text-muted mb-4">Kullanıcı Bilgileri</h6>
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="input-first-name">Ad</label>
                                        <input type="text" name="first_name" id="input-first-name" class="form-control" placeholder="Ad" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="input-last-name">Soyad</label>
                                        <input type="text" name="last_name" id="input-last-name" class="form-control" placeholder="Soyad" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- === DÜZELTME BURADA BAŞLIYOR === -->
                            
                            <!-- E-posta için kendi satırı -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label" for="input-email">E-posta Adresi</label>
                                        <input type="email" name="email" id="input-email" class="form-control" placeholder="test@example.com" value="<?php echo htmlspecialchars($user['email']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profil Resmi için kendi satırı -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label" for="profile_image">Profil Resmini Değiştir</label>
                                        <input type="file" name="profile_image" id="profile_image" class="form-control">
                                        <small class="form-text text-muted">Sadece .jpg, .jpeg, .png formatları. En fazla 2MB.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- === DÜZELTME BURADA BİTİYOR === -->

                        </div>
                        <hr class="my-4" />
                        
                        <h6 class="heading-small text-muted mb-4">Şifre Değiştir</h6>
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label" for="current_password">Mevcut Şifreniz</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="new_password">Yeni Şifre</label>
                                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Yeni şifrenizi girin..." autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="confirm_new_password">Yeni Şifre (Tekrar)</label>
                                        <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" placeholder="Yeni şifreyi tekrar girin..." autocomplete="new-password">
                                    </div>
                                </div>
                            </div>
                             <small class="form-text text-muted">Şifrenizi değiştirmek istemiyorsanız, yukarıdaki üç alanı da boş bırakın.</small>
                        </div>

                        <hr class="my-4" />
                        <div class="text-right">
                             <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

