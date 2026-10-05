            </div><!-- .col-md-10 -->
            </div><!-- .row -->
            </div><!-- .container-fluid -->

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var t = document.querySelector('.toast-notification');
                if (t) setTimeout(function() { t.remove(); }, 4500);
            });
            </script>
            </body>

            </html>
            <?php ob_end_flush(); ?>