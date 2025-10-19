<h2><?php echo (empty(array_filter($_GET))) ? 'Tüm Müsait Araçlar' : 'Arama Sonuçları'; ?></h2>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-2">
    <?php if (!empty($cars)): ?>
        <?php foreach ($cars as $car): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0">
                        <span class="badge bg-warning text-dark">
                            ★ <?php echo number_format($car['avg_rating'] ?? 0, 1); ?>
                        </span>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
                        <p class="card-text">
                            <strong>Yıl:</strong> <?php echo htmlspecialchars($car['year']); ?><br>
                            <strong>Yakıt:</strong> <?php echo htmlspecialchars($car['fuel_type']); ?><br>
                            <strong>Vites:</strong> <?php echo htmlspecialchars($car['transmission_type']); ?>
                        </p>
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center bg-transparent border-top-0">
                        <span class="fw-bold fs-5 text-primary"><?php echo htmlspecialchars($car['daily_rate']); ?> TL/Gün</span>
                        <a href="/rentacar/public/car_detail?id=<?php echo $car['car_id']; ?>" class="btn btn-primary">Detayları Gör</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning">Aradığınız kriterlere uygun araç bulunamadı.</div>
        </div>
    <?php endif; ?>
</div>

<?php 
if (isset($total_pages) && isset($current_page)) {
    require_once __DIR__ . '/../includes/pagination.php'; 
}
?>