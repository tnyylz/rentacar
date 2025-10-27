<?php
// Mevcut GET parametrelerini (filtreler) al, 'page' hariç
$query_params = $_GET;
unset($query_params['page']);

// URL'nin geri kalanını oluştur. Eğer başka parametreler varsa '&' ile başlar.
$query_string = http_build_query($query_params);
$base_url = '?' . $query_string;

if (isset($total_pages) && $total_pages > 1): ?>
<nav aria-label="Sayfa navigasyonu" class="mt-5">
    <ul class="pagination justify-content-center">
        <!-- Önceki Butonu -->
        <li class="page-item <?php echo (isset($current_page) && $current_page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo ($current_page - 1); ?>" aria-label="Önceki">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <!-- Sayfa Numaraları -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo (isset($current_page) && $current_page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Sonraki Butonu -->
        <li class="page-item <?php echo (isset($current_page) && $current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo ($current_page + 1); ?>" aria-label="Sonraki">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>
```


