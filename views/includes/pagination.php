<?php
// Mevcut GET parametrelerini (filtreler) al, 'page' hariç
$query_params = $_GET;
unset($query_params['page']);

// URL'nin geri kalanını oluştur. Eğer başka parametreler varsa '&' ile başlar.
$query_string = http_build_query($query_params);
$base_url = '?' . $query_string;

if ($total_pages > 1): ?>
<nav aria-label="Sayfa navigasyonu" class="mt-5">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo $current_page - 1; ?>">Önceki</a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_url; ?>&page=<?php echo $current_page + 1; ?>">Sonraki</a>
        </li>
    </ul>
</nav>
<?php endif; ?>