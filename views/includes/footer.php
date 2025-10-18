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
</body>
</html>