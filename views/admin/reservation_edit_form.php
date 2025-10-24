<?php require_once 'includes/header.php'; ?>

<div class="row">
    <div class="col-xl-8"> <!-- Sayfanın tamamını kaplamaması için 8'lik sütun -->
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Rezervasyon Detayı (#<?php echo $reservation['reservation_id']; ?>)</h3>
                    </div>
                    <div class="col text-right">
                        <a href="/rentacar/public/admin/reservations" class="btn btn-sm btn-primary">Rezervasyonlara Geri Dön</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Statik Rezervasyon Bilgileri -->
                <h6 class="heading-small text-muted mb-4">Rezervasyon Bilgileri</h6>
                <div class="pl-lg-4">
                    <div class="row">
                        <div class="col-lg-6">
                            <p class="mb-0"><strong>Müşteri:</strong></p>
                            <p><?php echo htmlspecialchars($reservation['user_full_name']); ?></p>
                        </div>
                        <div class="col-lg-6">
                            <p class="mb-0"><strong>Araç:</strong></p>
                            <p><?php echo htmlspecialchars($reservation['car_name']); ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <p class="mb-0"><strong>Alış Tarihi:</strong></p>
                            <p><?php echo date('d.m.Y H:i', strtotime($reservation['start_date'])); ?></p>
                        </div>
                        <div class="col-lg-6">
                            <p class="mb-0"><strong>Teslim Tarihi:</strong></p>
                            <p><?php echo date('d.m.Y H:i', strtotime($reservation['end_date'])); ?></p>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-lg-12">
                            <p class="h4 text-primary mt-3"><strong>Toplam Tutar: <?php echo number_format($reservation['total_price'], 2, ',', '.'); ?> TL</strong></p>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4" />

                <!-- Durum Güncelleme Formu -->
                <h6 class="heading-small text-muted mb-4">Durumu Güncelle</h6>
                <form action="/rentacar/public/admin/reservations/update" method="POST">
                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="status">Rezervasyon Durumu</Nabel>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Beklemede" <?php echo ($reservation['status'] == 'Beklemede') ? 'selected' : ''; ?>>Beklemede</option>
                                        <option value="Onaylandı" <?php echo ($reservation['status'] == 'Onaylandı') ? 'selected' : ''; ?>>Onaylandı</option>
                                        <option value="Tamamlandı" <?php echo ($reservation['status'] == 'Tamamlandı') ? 'selected' : ''; ?>>Tamamlandı</option>
                                        <option value="İptal Edildi" <?php echo ($reservation['status'] == 'İptal Edildi') ? 'selected' : ''; ?>>İptal Edildi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 align-self-center">
                                <!-- Butonu form-group içine alarak hizaladık -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Durumu Güncelle</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
