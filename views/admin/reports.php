<?php require_once 'includes/header.php'; ?>

<style>
    /* === Genel Sayfa Stil === */
    body {
        background: #f9fbfd;
        font-family: 'Poppins', sans-serif;
        color: #2b2d42;
        margin: 0;
        padding: 0;
    }

    h1.h2 {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .container {
        max-width: 1200px;
        margin: auto;
        padding-top: 2rem;
    }

    /* === KPI Kartları === */
    .kpi-card {
        border: none;
        border-radius: 18px;
        color: #111827;
        position: relative;
        overflow: hidden;
        background: linear-gradient(145deg, #ffffff, #f3f6fa);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .kpi-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .kpi-card .card-body {
        position: relative;
        z-index: 2;
    }

    .kpi-card::after {
        content: "";
        position: absolute;
        top: -30%;
        right: -30%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at center, rgba(0, 123, 255, 0.05), transparent 60%);
        z-index: 1;
    }

    .kpi-card h6 {
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05rem;
        color: #6b7280;
    }

    .kpi-card .display-6 {
        font-weight: 700;
        margin-top: 0.5rem;
        color: #111827;
    }

    /* KPI renkli kenar çizgileri */
    .kpi-card.primary { border-left: 6px solid #3b82f6; }
    .kpi-card.warning { border-left: 6px solid #f59e0b; }
    .kpi-card.success { border-left: 6px solid #10b981; }
    .kpi-card.info { border-left: 6px solid #06b6d4; }

    /* === Grafik Kartları === */
    .chart-card {
        border: none;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: 0 6px 35px rgba(0, 0, 0, 0.08);
    }

    .chart-card .card-header {
        background: linear-gradient(to right, #3b82f6, #06b6d4);
        color: #fff;
        font-weight: 600;
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
        padding: 0.75rem 1.25rem;
    }

    .chart-card .card-body {
        background-color: #fff;
        padding: 1.5rem;
    }

    /* === Tablolar === */
    table {
        border-radius: 12px;
        overflow: hidden;
        background-color: #fff;
    }

    thead {
        background: linear-gradient(to right, #3b82f6, #06b6d4);
        color: #fff;
        font-weight: 500;
    }

    tbody tr {
        transition: background-color 0.2s ease;
    }

    tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.08);
    }

    td {
        color: #374151;
    }

    /* === Butonlar === */
    .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.3);
    }

    /* === Animasyonlar === */
    .fade-in {
        opacity: 0;
        transform: translateY(10px);
        animation: fadeIn 0.6s ease forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>



<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Performans Paneli 📊</h1>
</div>

<div class="row">
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card text-bg-primary h-100 kpi-card">
            <div class="card-body">
                <h6 class="card-title">Ort. Kiralama Süresi</h6>
                <h3 class="fw-bold display-6"><?php echo number_format($avg_duration ?? 0, 1, ',', '.'); ?> Gün</h3>
                <div class="position-absolute bottom-0 end-0 p-3 fs-1 opacity-25">⏱️</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card text-bg-warning h-100 kpi-card">
            <div class="card-body">
                <h6 class="card-title">Genel Müşteri Memnuniyeti</h6>
                <h3 class="fw-bold display-6">★ <?php echo number_format($avg_rating ?? 0, 2, ',', '.'); ?></h3>
                <div class="position-absolute bottom-0 end-0 p-3 fs-1 opacity-25">⭐</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card text-bg-success h-100 kpi-card">
            <div class="card-body">
                <h6 class="card-title">Toplam Müşteri</h6>
                <h3 class="fw-bold display-6"><?php echo $total_customers ?? 0; ?></h3>
                <div class="position-absolute bottom-0 end-0 p-3 fs-1 opacity-25">👥</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card text-bg-info h-100 kpi-card">
            <div class="card-body">
                <h6 class="card-title">Filodaki Araç Sayısı</h6>
                <h3 class="fw-bold display-6"><?php echo $total_cars ?? 0; ?></h3>
                <div class="position-absolute bottom-0 end-0 p-3 fs-1 opacity-25">🚗</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card h-100 chart-card">
            <div class="card-header fw-bold">En Çok Kiralanan 5 Araç</div>
            <div class="card-body">
                <canvas id="topCarsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mb-4">
        <div class="card h-100 chart-card">
            <div class="card-header fw-bold">Aylık Kazanç Dağılımı (TL)</div>
            <div class="card-body">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6 mb-4">
        <h4 class="mb-3">En Karlı 5 Araç 🏆</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm align-middle">
                <thead class="table-dark"><tr><th>Araç</th><th class="text-end">Toplam Gelir</th></tr></thead>
                <tbody>
                    <?php if (!empty($most_profitable_cars)): foreach ($most_profitable_cars as $car): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($car['car_name']); ?></td>
                            <td class="text-end fw-bold"><?php echo number_format($car['total_revenue'], 2, ',', '.'); ?> TL</td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="2" class="text-center text-muted p-4">Gösterilecek veri bulunamadı.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <h4 class="mb-3">En Az Karlı 5 Araç 📉</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm align-middle">
                <thead class="table-secondary"><tr><th>Araç</th><th class="text-end">Toplam Gelir</th></tr></thead>
                <tbody>
                    <?php if (!empty($least_profitable_cars)): foreach ($least_profitable_cars as $car): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($car['car_name']); ?></td>
                            <td class="text-end fw-bold"><?php echo number_format($car['total_revenue'], 2, ',', '.'); ?> TL</td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="2" class="text-center text-muted p-4">Gösterilecek veri bulunamadı.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Controller'dan gelen verileri güvenli bir şekilde alıyoruz
    const topCarsData = <?php echo json_encode($top_cars_chart ?? []); ?>;
    const monthlyRevenueData = <?php echo json_encode($monthly_revenue_chart ?? []); ?>;

    // --- PASTA GRAFİĞİ: En Çok Kiralanan Araçlar ---
    const ctxTopCars = document.getElementById('topCarsChart');
    if (topCarsData.length > 0 && ctxTopCars) {
        const labels = topCarsData.map(item => item.car_name);
        const data = topCarsData.map(item => item.rental_count);
        
        new Chart(ctxTopCars, {
            type: 'doughnut', // 'pie' yerine 'doughnut' daha modern durabilir
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kiralama Sayısı',
                    data: data,
                    backgroundColor: ['#0d6efd', '#ffc107', '#198754', '#0dcaf0', '#6f42c1'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }

    // --- ÇUBUK GRAFİĞİ: Aylık Kazanç ---
    const ctxMonthlyRevenue = document.getElementById('monthlyRevenueChart');
    if (monthlyRevenueData.length > 0 && ctxMonthlyRevenue) {
        const labels = monthlyRevenueData.map(item => item.month);
        const data = monthlyRevenueData.map(item => item.total_revenue);

        new Chart(ctxMonthlyRevenue, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Aylık Kazanç (TL)',
                    data: data,
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>