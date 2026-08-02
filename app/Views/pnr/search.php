<section class="ri-page-header">
    <div class="container">
        <h1><i class="bi bi-ticket-perforated me-2"></i>PNR Status Tracker</h1>
        <p>Enter your 10-digit PNR number to check your booking status.</p>
    </div>
</section>

<section class="ri-section">
    <div class="container">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger ri-alert">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card ri-card ri-search-card shadow">
                    <div class="card-body p-4 p-md-5">
                        <form action="<?= site_url('pnr/track') ?>" method="post" class="row g-3">
                            <?= csrf_field() ?>
                            <div class="col-12">
                                <label for="pnr_number" class="form-label">PNR Number <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" inputmode="numeric" pattern="\d{10}" maxlength="10"
                                           class="form-control ri-pnr-input" id="pnr_number" name="pnr_number"
                                           placeholder="Enter 10-digit PNR"
                                           value="<?= esc(old('pnr_number')) ?>" required>
                                </div>
                                <div class="form-text">Found on your ticket / SMS confirmation, e.g. 2451098213.</div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn ri-btn-primary btn-lg px-5">
                                    <i class="bi bi-search me-2"></i>Check Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="ri-note mt-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Status is served from our cache when fresh, and refreshed automatically
                    from the live railway system in the background otherwise.
                </div>
            </div>
        </div>
    </div>
</section>
