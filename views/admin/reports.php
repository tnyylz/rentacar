<?php require_once 'includes/header.php'; ?>


<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats mb-3 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col mb-5">
                        <h5 class="card-title text-uppercase text-muted mb-0">Ort. Kiralama Süresi</h5>
                        <span class="h2 font-weight-bold mb-0"><?php echo number_format($avg_duration ?? 0, 1, ',', '.'); ?> Gün</span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
                            <i class="ni ni-time-alarm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col mb-5">
                        <h5 class="card-title text-uppercase text-muted mb-0">Genel Memnuniyet</h5>
                        <span class="h2 font-weight-bold mb-0">★ <?php echo number_format($overall_avg_rating ?? 0, 2, ',', '.'); ?></span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                            <i class="ni ni-like-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col mb-5">
                        <h5 class="card-title text-uppercase text-muted mb-0">Toplam Müşteri</h5>
                        <span class="h2 font-weight-bold mb-0"><?php echo $total_customers ?? 0; ?></span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                            <i class="ni ni-single-02"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
                <div class="row">
                    <div class="col mb-5">
                        <h5 class="card-title text-uppercase text-muted mb-0">Filodaki Araç Sayısı</h5>
                        <span class="h2 font-weight-bold mb-0"><?php echo $total_cars ?? 0; ?></span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                            <i class="ni ni-delivery-fast"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- === KPI KARTLARI BÖLÜMÜ SONU === -->


<!-- Grafiksel Raporlar -->
<div class="row mt-5">
    <div class="col-xl-6 mb-5 mb-xl-0">
        <div class="card bg-default">
            <div class="card-header bg-transparent">
                <h5 class="h3 text-white mb-0">En Çok Kiralanan Araçlar</h5>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="topCarsChart" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header bg-transparent">
                <h5 class="h3 mb-0">Aylık Kazanç Dağılımı</h5>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="monthlyRevenueChart" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tablo Raporları -->
<div class="row mt-5">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="mb-0">En Karlı 5 Araç 🏆</h3>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Araç</th>
                            <th scope="col" class="text-right">Toplam Gelir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($most_profitable_cars) && !empty($most_profitable_cars)): ?>
                            <?php foreach ($most_profitable_cars as $car): ?>
                                <tr>
                                    <th scope="row"><?php echo htmlspecialchars($car['car_name']); ?></th>
                                    <td class="text-right fw-bold"><?php echo number_format($car['total_revenue'], 2, ',', '.'); ?> TL</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-center text-muted p-4">Veri yok.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="mb-0">En Az Karlı 5 Araç 📉</h3>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Araç</th>
                            <th scope="col" class="text-right">Toplam Gelir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($least_profitable_cars) && !empty($least_profitable_cars)): ?>
                            <?php foreach ($least_profitable_cars as $car): ?>
                                <tr>
                                    <th scope="row"><?php echo htmlspecialchars($car['car_name']); ?></th>
                                    <td class="text-right fw-bold"><?php echo number_format($car['total_revenue'], 2, ',', '.'); ?> TL</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-center text-muted p-4">Veri yok.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<!-- Sayfaya özel JavaScript (Chart.js) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // Controller'dan gelen doğru değişken isimlerini kullanıyoruz
    const topCarsData = <?php echo json_encode($top_cars_chart ?? []); ?>;
    const monthlyRevenueData = <?php echo json_encode($monthly_revenue_chart ?? []); ?>;

    // --- PASTA GRAFİĞİ: En Çok Kiralanan Araçlar ---
    const ctxTopCars = document.getElementById('topCarsChart');
    if (topCarsData.length > 0 && ctxTopCars) {
        const labels = topCarsData.map(item => item.car_name);
        const data = topCarsData.map(item => item.rental_count);
        
        new Chart(ctxTopCars, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kiralama Sayısı',
                    data: data,
                    backgroundColor: ['#5e72e4', '#fb6340', '#2dce89', '#11cdef', '#f5365c'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { color: '#adb5bd' } // Argon temasına uygun
                    }
                }
            }
        });
    }

    // --- ÇUBUK GRAFİĞİ: Aylık Kazanç ---
    const ctxMonthlyRevenue = document.getElementById('monthlyRevenueChart');
    if (monthlyRevenueData.length > 0 && ctxMonthlyRevenue) {
        monthlyRevenueData.reverse();
        const labels = monthlyRevenueData.map(item => item.month);
        const data = monthlyRevenueData.map(item => item.total_revenue);

        new Chart(ctxMonthlyRevenue, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Aylık Kazanç (TL)',
                    data: data,
                    backgroundColor: 'rgba(45, 206, 137, 0.7)', // Argon yeşili
                    borderColor: 'rgba(45, 206, 137, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { color: '#8898aa' }
                    },
                    x: {
                        ticks: { color: '#8898aa' }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>

