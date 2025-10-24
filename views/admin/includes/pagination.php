<?php
// Mevcut GET parametrelerini (filtreler) al, 'page' hariç
$query_params = $_GET;
unset($query_params['page']);

// URL'nin geri kalanını oluştur.
$query_string = http_build_query($query_params);
$base_url = '?' . $query_string;

if ($total_pages > 1): ?>
<nav aria-label="Sayfa navigasyonu" class="mt-5">
    <ul class="pagination justify-content-center">
        <!-- Önceki Sayfa Butonu -->
        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo $current_page - 1; ?>" aria-label="Önceki">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <!-- Sayfa Numaraları -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Sonraki Sayfa Butonu -->
        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo $current_page + 1; ?>" aria-label="Sonraki">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>
