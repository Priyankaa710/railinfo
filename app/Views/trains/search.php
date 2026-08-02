<section class="ri-page-header">
    <div class="container">
        <h1><i class="bi bi-search me-2"></i>Train Schedule Checker</h1>
        <p>Find trains between any two stations, complete with seat availability and fare.</p>
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

        <div class="card ri-card ri-search-card shadow">
            <div class="card-body p-4 p-md-5">
                <form action="<?= site_url('trains/search') ?>" method="post" class="row g-3" autocomplete="off" novalidate>
                    <?= csrf_field() ?>

                    <div class="col-md-4 position-relative">
                        <label for="source" class="form-label">From Station <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" class="form-control form-control-lg ri-station-input"
                                   id="source" name="source" placeholder="Station name or code"
                                   value="<?= esc(old('source') ?? ($_GET['source'] ?? '')) ?>"
                                   required data-autocomplete="station">
                        </div>
                        <div class="ri-suggestions" id="source_suggestions"></div>
                    </div>

                    <div class="col-md-4 position-relative">
                        <label for="destination" class="form-label">To Station <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="text" class="form-control form-control-lg ri-station-input"
                                   id="destination" name="destination" placeholder="Station name or code"
                                   value="<?= esc(old('destination') ?? ($_GET['destination'] ?? '')) ?>"
                                   required data-autocomplete="station">
                        </div>
                        <div class="ri-suggestions" id="destination_suggestions"></div>
                    </div>

                    <div class="col-md-4">
                        <label for="travel_date" class="form-label">Date of Journey <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" class="form-control form-control-lg" id="travel_date"
                                   name="travel_date" min="<?= date('Y-m-d') ?>"
                                   value="<?= esc(old('travel_date') ?? date('Y-m-d')) ?>" required>
                        </div>
                    </div>

                    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-2">
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Tip: use the station code (e.g. NDLS) for faster matches.</small>
                        <button type="submit" class="btn ri-btn-primary btn-lg px-5">
                            <i class="bi bi-search me-2"></i>Search Trains
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-5">
            <h5 class="ri-section__title mb-3">Popular Routes</h5>
            <div class="row g-3">
                <?php foreach ($sources as $route): ?>
                    <div class="col-md-6 col-lg-3">
                        <a class="ri-route-card" href="<?= site_url('trains') ?>?source=<?= esc($route['from']) ?>&destination=<?= esc($route['to']) ?>">
                            <div class="ri-route-card__cities">
                                <span><?= esc($route['fromName']) ?><small><?= esc($route['from']) ?></small></span>
                                <i class="bi bi-arrow-right"></i>
                                <span><?= esc($route['toName']) ?><small><?= esc($route['to']) ?></small></span>
                            </div>
                            <span class="ri-route-card__cta">Check trains <i class="bi bi-chevron-right"></i></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
