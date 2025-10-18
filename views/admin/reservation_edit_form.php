<?php require_once 'includes/header.php'; ?>

<h1 class="h2">Rezervasyon Detayını Düzenle (#<?php echo $reservation['reservation_id']; ?>)</h1>

<div class="card mt-4">
    <div class="card-header">
        Rezervasyon Bilgileri
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><strong>Müşteri:</strong> <?php echo htmlspecialchars($reservation['user_full_name']); ?></li>
        <li class="list-group-item"><strong>Araç:</strong> <?php echo htmlspecialchars($reservation['car_name']); ?></li>
        <li class="list-group-item"><strong>Alış Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($reservation['start_date'])); ?></li>
        <li class="list-group-item"><strong>Teslim Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($reservation['end_date'])); ?></li>
        <li class="list-group-item"><strong>Toplam Tutar:</strong> <?php echo htmlspecialchars($reservation['total_price']); ?> TL</li>
    </ul>
</div>

<form action="/rentacar/public/admin/reservations/update" method="POST" class="mt-4">
    <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">

    <div class="mb-3">
        <label for="status" class="form-label"><strong>Rezervasyon Durumu</strong></label>
        <select class="form-select" id="status" name="status">
            <option value="Beklemede" <?php echo ($reservation['status'] == 'Beklemede') ? 'selected' : ''; ?>>Beklemede</option>
            <option value="Onaylandı" <?php echo ($reservation['status'] == 'Onaylandı') ? 'selected' : ''; ?>>Onaylandı</option>
            <option value="Tamamlandı" <?php echo ($reservation['status'] == 'Tamamlandı') ? 'selected' : ''; ?>>Tamamlandı</option>
            <option value="İptal Edildi" <?php echo ($reservation['status'] == 'İptal Edildi') ? 'selected' : ''; ?>>İptal Edildi</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Durumu Güncelle</button>
    <a href="/rentacar/public/admin/reservations" class="btn btn-secondary">Geri Dön</a>
</form>

<?php require_once 'includes/footer.php'; ?>    