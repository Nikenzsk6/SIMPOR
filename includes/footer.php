            </div><!-- .col-md-10 -->
            </div><!-- .row -->
            </div><!-- .container-fluid -->

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var t = document.querySelector('.toast-notification');
                if (t) setTimeout(function() { t.remove(); }, 4500);
            });
            </script>

            <!-- Konfirmasi logout (RTM TR120-TR126) -->
            <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="logoutModalLabel"><i class="bi bi-box-arrow-right"></i> Konfirmasi Logout</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin keluar dari aplikasi SIMPOR?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <a href="<?= BASE_URL ?>logout.php" class="btn btn-danger">Ya, Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            </body>

            </html>
            <?php ob_end_flush(); ?>