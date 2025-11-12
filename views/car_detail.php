<?php require_once 'includes/header.php'; ?>
<?php if (isset($car)): ?>
    
    <div class="row my-3 g-5">
        
        <div class="col-lg-7">
            <h1 class="mb-1"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>
            <p class="fs-4 text-muted"><?php echo htmlspecialchars($car['year']); ?> Model</p>

            <div class="mb-3">
                <?php if (isset($rating_info) && $rating_info['review_count'] > 0): ?>
                    <span class="badge bg-warning text-dark fs-6">
                        ★ <?php echo number_format($rating_info['avg_rating'], 1); ?>
                    </span>
                    <span class="text-muted ms-2">(<?php echo $rating_info['review_count']; ?> değerlendirme)</span>
                <?php else: ?>
                    <span class="text-muted">Henüz puanlanmamış</span>
                <?php endif; ?>
            </div>

            <img src="<?php echo !empty($car['image_url']) ? htmlspecialchars($car['image_url']) : 'https://via.placeholder.com/600x400.png?text=Ara%C3%A7+Resmi'; ?>" 
                 alt="<?php echo htmlspecialchars($car['brand']); ?>" class="img-fluid rounded mb-4">
            
            <ul class="list-group list-group-flush fs-5">
                <li class="list-group-item"><strong>Yakıt Tipi:</strong> <?php echo htmlspecialchars($car['fuel_type']); ?></li>
                <li class="list-group-item"><strong>Vites Tipi:</strong> <?php echo htmlspecialchars($car['transmission_type']); ?></li>
                <li class="list-group-item"><strong>Durum:</strong> 
                    <span class="fw-bold <?php echo $car['status'] === 'Müsait' ? 'text-success' : 'text-danger'; ?>">
                        <?php echo htmlspecialchars($car['status']); ?>
                    </span>
                </li>
            </ul>

            <div class="pricing my-4">
                <h2 class="fw-bold"><?php echo htmlspecialchars($car['daily_rate']); ?> TL <small class="text-muted fs-6">/ Gün</small></h2>
            </div>
            
            <div class="reservation-action">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($car['status'] === 'Müsait'): ?>
                        <button class="btn btn-success btn-lg w-100" type="button" data-bs-toggle="collapse" data-bs-target="#reservationFormCollapse">Hemen Kirala</button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg w-100" type="button" disabled>Bu Araç Şu Anda <?php echo htmlspecialchars($car['status']); ?></button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="#" class="btn btn-primary btn-lg w-100 open-login-modal">Kiralamak için Giriş Yapın</a>
                <?php endif; ?>
            </div>
            
            <div class="collapse mt-4" id="reservationFormCollapse">
                <div class="card card-body" style="background-color: #f8f9fa;">
                    <h3>Rezervasyon Detayları</h3>
                    <form action="/rentacar/public/create-reservation" method="POST">
                        <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                        <input type="hidden" name="daily_rate" value="<?php echo $car['daily_rate']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pickup_location" class="form-label">Alış Lokasyonu</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($car['location_name'] ?? 'Belirtilmemiş'); ?>" disabled>
                                <input type="hidden" name="pickup_location_id" value="<?php echo $car['current_location_id']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dropoff_location" class="form-label">Teslim Lokasyonu</label>
                                <select class="form-select" id="dropoff_location" name="dropoff_location_id" required>
                                    <option selected disabled value="">Seçiniz...</option>
                                    <?php 
                                    $conn = \App\Database::getInstance()->getConnection();
                                    $locations = $conn->query("SELECT * FROM locations WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);
                                    foreach ($locations as $location): ?>
                                        <option value="<?php echo $location['location_id']; ?>"><?php echo htmlspecialchars($location['location_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-7 mb-3">
                                <label for="reservation_pickup_date" class="form-label">Alış Tarihi</label>
                                <input type="text" class="form-control" id="reservation_pickup_date" name="pickup_date" placeholder="Tarih Seçin" required>
                            </div>
                            <div class="col-lg-5 mb-3">
                                <label for="reservation_pickup_time" class="form-label">Alış Saati</label>
                                <select name="pickup_time" id="reservation_pickup_time" class="form-select">
                                    <?php for ($h = 0; $h < 24; $h++): $time = sprintf('%02d:00', $h); ?>
                                        <option value="<?php echo $time; ?>" <?php echo ('10:00' == $time) ? 'selected' : ''; ?>><?php echo $time; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-7 mb-3">
                                <label for="reservation_return_date" class="form-label">İade Tarihi</label>
                                <input type="text" class="form-control" id="reservation_return_date" name="return_date" placeholder="Tarih Seçin" required>
                            </div>
                            <div class="col-lg-5 mb-3">
                                <label for="reservation_return_time" class="form-label">İade Saati</label>
                                 <select name="return_time" id="reservation_return_time" class="form-select">
                                    <?php for ($h = 0; $h < 24; $h++): $time = sprintf('%02d:00', $h); ?>
                                        <option value="<?php echo $time; ?>" <?php echo ('10:00' == $time) ? 'selected' : ''; ?>><?php echo $time; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div id="price-calculation-result" class="my-3 text-center"></div>
                        <button type="submit" class="btn btn-primary w-100">Rezervasyonu Onayla</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h4>Müsaitlik Takvimi</h4>
            <div class="calendar-aspect-ratio-wrapper">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <h2>Değerlendirmeler (<?php echo $rating_info['review_count'] ?? 0; ?>)</h2>

    <?php if (isset($eligible_reservation_id) && $eligible_reservation_id): ?>
        <div class="card mb-4">
            <div class="card-header">Bu Araç Hakkında Değerlendirmenizi Paylaşın</div>
            <div class="card-body">
                <form action="/rentacar/public/add-review" method="POST">
                    <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                    <input type="hidden" name="reservation_id" value="<?php echo $eligible_reservation_id; ?>">
                    <div class="mb-3"><label for="rating" class="form-label">Puanınız (1-5)</label><select name="rating" id="rating" class="form-select" required><option value="5">★★★★★ (5)</option><option value="4">★★★★☆ (4)</option><option value="3">★★★☆☆ (3)</option><option value="2">★★☆☆☆ (2)</option><option value="1">★☆☆☆☆ (1)</option></select></div>
                    <div class="mb-3"><label for="comment" class="form-label">Yorumunuz</label><textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Deneyimlerinizi paylaşın..."></textarea></div>
                    <button type="submit" class="btn btn-primary">Yorumu Gönder</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($reviews) && !empty($reviews)): ?>
        <?php foreach($reviews as $review): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($review['first_name'] . ' ' . substr($review['last_name'], 0, 1) . '.'); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        <span class="badge bg-warning text-dark">★ <?php echo $review['rating']; ?></span>
                        <small class="ms-2"><?php echo date('d.m.Y', strtotime($review['created_at'])); ?></small>
                    </h6>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">Bu araç için henüz bir yorum yapılmamış.</p>
    <?php endif; ?>

<?php else: ?>
    <p>Araç bilgileri yüklenemedi.</p>
<?php endif; ?>


<?php if (isset($car)): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                locale: 'tr',
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev', center: 'title', right: 'next' },
                events: '/rentacar/public/api/reservations?car_id=<?php echo $car['car_id']; ?>'
            });
            calendar.render();
        }
    });
</script>

<!-- Anlık Fiyat Hesaplama Script'i (GÜNCELLENDİ) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Form elemanlarını seç
        const pickupDateInput = document.getElementById('reservation_pickup_date');
        const returnDateInput = document.getElementById('reservation_return_date');
        const pickupTimeSelect = document.getElementById('reservation_pickup_time');
        const returnTimeSelect = document.getElementById('reservation_return_time');
        const priceResultDiv = document.getElementById('price-calculation-result');
        const dailyRate = document.querySelector('input[name="daily_rate"]').value;

        // Fiyat hesaplama fonksiyonu
        function calculatePrice() {
            const startDate = pickupDateInput.value;
            const endDate = returnDateInput.value;
            const startTime = pickupTimeSelect.value;
            const endTime = returnTimeSelect.value;

            // Sadece tüm alanlar doluysa ve tarihler geçerliyse istek gönder
            if (startDate && endDate && startTime && endTime) {
                
                // Litepicker D.M.Y formatında verdiği için, JS'in anlayacağı Y-M-D formatına çevir
                const startDateTime = new Date(startDate.split('.').reverse().join('-') + 'T' + startTime);
                const endDateTime = new Date(endDate.split('.').reverse().join('-') + 'T' + endTime);

                if (startDateTime >= endDateTime) {
                    priceResultDiv.innerHTML = `<div class="alert alert-danger">İade tarihi, alış tarihinden sonra olmalıdır.</div>`;
                    return;
                }
                
                priceResultDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
                
                // API'ye D.M.Y formatında gönder (API Controller'ımız bu formatı bekliyor)
                const apiUrl = `/rentacar/public/api/calculate-price?pickup_date=${startDate}&pickup_time=${startTime}&return_date=${endDate}&return_time=${endTime}&daily_rate=${dailyRate}`;

                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            priceResultDiv.innerHTML = `<div class="alert alert-info"><strong>${data.days} gün</strong> için toplam tutar: <strong class="fs-5">${data.total_price} TL</strong></div>`;
                        } else {
                            priceResultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    });
            } else {
                priceResultDiv.innerHTML = '';
            }
        }
        
        // Saat seçimleri değiştiğinde fiyatı yeniden hesapla
        pickupTimeSelect.addEventListener('change', calculatePrice);
        returnTimeSelect.addEventListener('change', calculatePrice);

        // Akıllı Takvimi (Litepicker) Başlat
        if (pickupDateInput && returnDateInput) {
            
            // --- DÜZELTME BAŞLANGICI ---
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Bugünü seçilebilir yap
            // --- DÜZELTME SONU ---

            const picker = new Litepicker({
                element: pickupDateInput,
                elementEnd: returnDateInput,
                singleMode: false,
                allowRepick: true,
                format: 'DD.MM.YYYY',
                minDate: today, // Düzeltilmiş tarihi kullan
                numberOfMonths: 1, 
                dropdowns: {
                    minYear: new Date().getFullYear(),
                    maxYear: new Date().getFullYear() + 1,
                    months: true,
                    years: true,
                },
                tooltipText: {"one": "gün", "other": "gün"},
                lang: 'tr-TR',
                setup: (picker) => {
                    // Takvimden bir tarih aralığı seçildiğinde
                    picker.on('selected', (date1, date2) => {
                        // --- DÜZELTME BAŞLANGICI ---
                        // Input'ları manuel olarak güncelle (hatanın çözümü)
                        pickupDateInput.value = date1.format('DD.MM.YYYY');
                        returnDateInput.value = date2.format('DD.MM.YYYY');
                        // Fiyat hesaplama fonksiyonunu BURADA tetikle
                        calculatePrice();
                        // --- DÜZELTME SONU ---
                    });
                }
            });
        }
    });
</script>
<?php endif; ?>


<?php require_once 'includes/footer.php'; ?>