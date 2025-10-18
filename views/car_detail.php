<?php 
require_once 'includes/header.php'; 

// --- DÜZELTME BURADA ---
// Lokasyonları sayfanın en başında, SADECE BİR KEZ ve DOĞRU YOL ile veritabanından çekelim.
$locations_array = [];
require __DIR__ . '/../config/db.php'; // İki nokta yerine tek nokta kullanıldı.
$locations_result = $conn->query("SELECT * FROM locations WHERE status = 'Active'");
if ($locations_result) {
    $locations_array = $locations_result->fetch_all(MYSQLI_ASSOC);
}
$conn->close(); 
// --- DÜZELTME SONU ---
?>

<?php if (isset($car)): ?>
    <div class="row">
        <div class="col-md-7">
            <img src="<?php echo !empty($car['image_url']) ? htmlspecialchars($car['image_url']) : 'https://via.placeholder.com/600x400.png?text=Ara%C3%A7+Resmi'; ?>" 
                 alt="<?php echo htmlspecialchars($car['brand']); ?>" class="img-fluid rounded">
        </div>
        <div class="col-md-5">
            <h1 class="mb-3"><?php echo htmlspecialchars($car['brand']) . ' ' . htmlspecialchars($car['model']); ?></h1>
            <p class="fs-4 text-muted"><?php echo htmlspecialchars($car['year']); ?> Model</p>
            
            <ul class="list-group list-group-flush fs-5">
                <li class="list-group-item"><strong>Yakıt Tipi:</strong> <?php echo htmlspecialchars($car['fuel_type']); ?></li>
                <li class="list-group-item"><strong>Vites Tipi:</strong> <?php echo htmlspecialchars($car['transmission_type']); ?></li>
                <li class="list-group-item"><strong>Durum:</strong> 
                    <span class="fw-bold <?php echo $car['status'] === 'Müsait' ? 'text-success' : 'text-danger'; ?>">
                        <?php echo htmlspecialchars($car['status']); ?>
                    </span>
                </li>
            </ul>

            <div class="pricing mt-4">
                <h2 class="fw-bold"><?php echo htmlspecialchars($car['daily_rate']); ?> TL <small class="text-muted fs-6">/ Gün</small></h2>
            </div>
            
            <div class="reservation-action mt-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($car['status'] === 'Müsait'): ?>
                        <button class="btn btn-success btn-lg w-100" type="button" data-bs-toggle="collapse" data-bs-target="#reservationFormCollapse">
                            Hemen Kirala
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg w-100" type="button" disabled>
                            Bu Araç Şu Anda <?php echo htmlspecialchars($car['status']); ?>
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="#" class="btn btn-primary btn-lg w-100 open-login-modal">Kiralamak için Giriş Yapın</a>
                <?php endif; ?>
            </div>
            
            <div class="collapse mt-4" id="reservationFormCollapse">
                <div class="card card-body">
                    <h3>Rezervasyon Detayları</h3>
                    <form action="/rentacar/public/create-reservation" method="POST">
                        <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                        <input type="hidden" name="daily_rate" value="<?php echo $car['daily_rate']; ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Alış Tarihi ve Saati</label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Teslim Tarihi ve Saati</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pickup_location" class="form-label">Alış Lokasyonu</label>
                                <select class="form-select" id="pickup_location" name="pickup_location_id" required>
                                    <option selected disabled value="">Seçiniz...</option>
                                    <?php
                                    foreach ($locations_array as $location) {
                                        echo "<option value='{$location['location_id']}'>" . htmlspecialchars($location['location_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dropoff_location" class="form-label">Teslim Lokasyonu</label>
                                <select class="form-select" id="dropoff_location" name="dropoff_location_id" required>
                                    <option selected disabled value="">Seçiniz...</option>
                                    <?php
                                    foreach ($locations_array as $location) {
                                        echo "<option value='{$location['location_id']}'>" . htmlspecialchars($location['location_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Rezervasyonu Onayla</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Araç bilgileri yüklenemedi.</p>
<?php endif; ?>


<?php require_once 'includes/pagination.php'; ?>
<?php require_once 'includes/footer.php'; ?>