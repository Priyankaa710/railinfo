</main>

<footer class="ri-footer">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4">
                <div class="ri-brand ri-brand--footer mb-3">
                    <span class="ri-brand__icon"><i class="bi bi-train-front-fill"></i></span>
                    <span class="ri-brand__text">RailInfo</span>
                </div>
                <p class="ri-footer__desc">
                    RailInfo is an informational portal for checking train schedules and PNR
                    status. Data shown may be cached for offline availability and should be
                    verified against the official railway enquiry system before travel.
                </p>
                <div class="ri-footer__badges">
                    <span class="badge ri-badge-govt"><i class="bi bi-patch-check-fill me-1"></i>GovTech Portal</span>
                    <span class="badge ri-badge-govt"><i class="bi bi-lock-fill me-1"></i>Secure</span>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="ri-footer__heading">Explore</h6>
                <ul class="list-unstyled ri-footer__links">
                    <li><a href="<?= site_url('/') ?>">Home</a></li>
                    <li><a href="<?= site_url('trains') ?>">Train Schedule</a></li>
                    <li><a href="<?= site_url('pnr') ?>">PNR Status</a></li>
                    <li><a href="<?= site_url('about') ?>">About</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="ri-footer__heading">Services</h6>
                <ul class="list-unstyled ri-footer__links">
                    <li><a href="#">Seat Availability</a></li>
                    <li><a href="#">Live Train Status</a></li>
                    <li><a href="#">Fare Enquiry</a></li>
                    <li><a href="#">Station Directory</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="ri-footer__heading">Helpline</h6>
                <ul class="list-unstyled ri-footer__links">
                    <li><i class="bi bi-telephone-fill me-2"></i>139 (Rail Enquiry, 24x7)</li>
                    <li><i class="bi bi-envelope-fill me-2"></i>support@railinfo.gov.in</li>
                    <li><i class="bi bi-geo-alt-fill me-2"></i>Rail Bhawan, New Delhi</li>
                </ul>
            </div>
        </div>
        <hr class="ri-footer__divider">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 ri-footer__bottom">
            <span>&copy; <?= date('Y') ?> RailInfo Portal. All rights reserved.</span>
            <span>Content is for informational purposes only &middot; Not affiliated with IRCTC bookings</span>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle (JS + Popper) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
