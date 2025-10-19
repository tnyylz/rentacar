<footer class="pt-4 my-md-5 pt-md-5 border-top text-center">
            <p>&copy; <?php echo date('Y'); ?> Araç Kiralama Projesi</p>
        </footer>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Giriş Yap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/rentacar/public/login-submit" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta Adresi</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
   integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
   crossorigin=""></script>
    <script>
        // Login modal'ını tetiklemek için script
        const loginModalBtn = document.getElementById('loginModalBtn');
        if (loginModalBtn) {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModalBtn.addEventListener('click', function (event) {
                event.preventDefault();
                loginModal.show();
            });
        }
    </script>
    <script>
            var map = L.map('map').setView([38.4237, 27.1428], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
            
            // Araç verilerini ve raptiyelerini saklamak için bir dizi
            let carMarkers = [];

            // Sunucudaki API'den araç verilerini çek
            fetch('/rentacar/public/api/cars')
                .then(response => response.json())
                .then(cars => {
                    cars.forEach(car => {
                        var marker = L.marker([car.latitude, car.longitude]).addTo(map);
                        
                        var popupContent = `
                            <strong>${car.brand} ${car.model}</strong><br>
                            Günlük Ücret: ${car.daily_rate} TL<br>
                            <a href="/rentacar/public/car_detail?id=${car.car_id}">Detayları Gör</a>
                        `;
                        marker.bindPopup(popupContent);
                        
                        // Daha sonra mesafeyi hesaplamak için arabayı ve raptiyesini sakla
                        carMarkers.push({ car: car, marker: marker });
                    });
                })
                .catch(error => console.error('Harita verileri yüklenirken hata oluştu:', error));

            // --- "KONUMUMU BUL" ÖZELLİĞİ ---

            // İki koordinat arasındaki mesafeyi kilometre olarak hesaplayan fonksiyon (Haversine formülü)
            function getDistance(lat1, lon1, lat2, lon2) {
                const R = 6371; // Dünya'nın yarıçapı (km)
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                          Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            // Butonu seç ve tıklama olayı ekle
            const locateBtn = document.getElementById('locate-btn');
            locateBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Tarayıcınız konum bulma özelliğini desteklemiyor.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    // Konum başarıyla alındı
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Haritayı kullanıcının konumuna odakla ve bir raptiye ekle
                    map.setView([lat, lon], 14); // Daha yakından bak
                    L.marker([lat, lon], { title: 'Siz Buradasınız' }).addTo(map)
                      .bindPopup('<b>Siz Buradasınız!</b>').openPopup();

                    // En yakın aracı bul
                    let closestCar = null;
                    let minDistance = Infinity;

                    carMarkers.forEach(item => {
                        const distance = getDistance(lat, lon, item.car.latitude, item.car.longitude);
                        if (distance < minDistance) {
                            minDistance = distance;
                            closestCar = item;
                        }
                    });

                    // Eğer bir araç bulunduysa, onun raptiyesinin balonunu aç
                    if (closestCar) {
                        setTimeout(function() {
                             closestCar.marker.openPopup();
                        }, 1000); // 1 saniye sonra
                        alert('Size en yakın araç ' + minDistance.toFixed(2) + ' km uzaklıkta bulundu!');
                    }

                }, function() {
                    // Konum alınamadı (kullanıcı reddetti vb.)
                    alert('Konum bilgisi alınamadı. Lütfen tarayıcı izinlerinizi kontrol edin.');
                });
            });

        </script>

<script>
    // Arama formunu seç
    const filterForm = document.querySelector('form[action="/rentacar/public/home"]');
    // Araç listesinin bulunduğu alanı seç
    const carListContainer = document.getElementById('car-list-container');

    if (filterForm && carListContainer) {
        filterForm.addEventListener('submit', function(event) {
            // Formun normal şekilde gönderilip sayfanın yenilenmesini engelle
            event.preventDefault();

            // Form verilerini al
            const formData = new FormData(filterForm);
            // URL parametrelerine çevir (örn: ?start_date=...&category_id=...)
            const params = new URLSearchParams(formData);

            // Yükleniyor... mesajı göster (isteğe bağlı)
            carListContainer.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>';

            // API'ye AJAX isteği gönder
            fetch(`/rentacar/public/api/filter-cars?${params.toString()}`)
                .then(response => response.text())
                .then(html => {
                    // Gelen HTML içeriğiyle araç listesi alanını güncelle
                    carListContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Filtreleme sırasında hata:', error);
                    carListContainer.innerHTML = '<div class="alert alert-danger">Araçlar yüklenirken bir hata oluştu.</div>';
                });
        });
    }
</script>



</body>
</html>