<section class="ri-hero">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <span class="ri-eyebrow"><i class="bi bi-patch-check-fill me-1"></i>Official Informational Portal</span>
                <h1 class="ri-hero__title">Track your journey.<br>Know before you go.</h1>
                <p class="ri-hero__subtitle">
                    Search live train schedules, check seat availability, and get real-time
                    PNR status — all cached for quick, reliable access across India's rail network.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="#quick-search" class="btn ri-btn-primary btn-lg">
                        <i class="bi bi-search me-2"></i>Check Train Schedule
                    </a>
                    <a href="<?= site_url('pnr') ?>" class="btn ri-btn-outline-light btn-lg">
                        <i class="bi bi-ticket-perforated me-2"></i>Track PNR Status
                    </a>
                </div>
                <div class="ri-hero__stats">
                    <div>
                        <strong><?= esc($stats['trains']) ?>+</strong>
                        <span>Trains Covered</span>
                    </div>
                    <div>
                        <strong><?= esc($stats['stations']) ?>+</strong>
                        <span>Stations</span>
                    </div>
                    <div>
                        <strong><?= esc($stats['daily']) ?></strong>
                        <span>Daily Passengers</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ri-hero__art">
                    <i class="bi bi-train-front"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="quick-search" class="ri-quick-search">
    <div class="container">
        <div class="card ri-card ri-search-card shadow-lg">
            <div class="card-body p-4 p-md-5">
                <ul class="nav nav-tabs ri-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-schedule" type="button">
                            <i class="bi bi-search me-2"></i>Train Schedule
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pnr" type="button">
                            <i class="bi bi-ticket-perforated me-2"></i>PNR Status
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- SCHEDULE TAB -->
                    <div class="tab-pane fade show active" id="tab-schedule" role="tabpanel">
                        <form action="<?= site_url('trains/search') ?>" method="post" class="row g-3" autocomplete="off">
                            <?= csrf_field() ?>
                            <div class="col-md-4 position-relative">
                                <label for="home_source" class="form-label">From Station</label>
                                <input type="text" class="form-control form-control-lg ri-station-input"
                                       id="home_source" name="source" placeholder="e.g. New Delhi (NDLS)" required
                                       data-autocomplete="station">
                                <div class="ri-suggestions" id="home_source_suggestions"></div>
                            </div>
                            <div class="col-md-4 position-relative">
                                <label for="home_destination" class="form-label">To Station</label>
                                <input type="text" class="form-control form-control-lg ri-station-input"
                                       id="home_destination" name="destination" placeholder="e.g. Mumbai Central (BCT)" required
                                       data-autocomplete="station">
                                <div class="ri-suggestions" id="home_destination_suggestions"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="home_date" class="form-label">Date of Journey</label>
                                <input type="date" class="form-control form-control-lg" id="home_date"
                                       name="travel_date" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn ri-btn-primary btn-lg px-5">
                                    <i class="bi bi-search me-2"></i>Search Trains
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- PNR TAB -->
                    <div class="tab-pane fade" id="tab-pnr" role="tabpanel">
                        <form action="<?= site_url('pnr/track') ?>" method="post" class="row g-3">
                            <?= csrf_field() ?>
                            <div class="col-md-8">
                                <label for="home_pnr" class="form-label">10-Digit PNR Number</label>
                                <input type="text" inputmode="numeric" pattern="\d{10}" maxlength="10"
                                       class="form-control form-control-lg" id="home_pnr" name="pnr_number"
                                       placeholder="e.g. 2451098213" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn ri-btn-primary btn-lg w-100">
                                    <i class="bi bi-ticket-perforated me-2"></i>Check Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ri-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="ri-section__title mb-0">Popular Routes</h2>
            <a href="<?= site_url('trains') ?>" class="ri-link-more">View all routes <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            <?php foreach ($popularRoutes as $route): ?>
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
</section>

<?php if (! empty($recentSearches)): ?>
<section class="ri-section ri-section--muted">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="ri-section__title mb-0"><i class="bi bi-clock-history me-2"></i>Your Recent Searches</h2>
            <a href="<?= site_url('trains/history/clear') ?>" class="ri-link-more text-danger">Clear history <i class="bi bi-x-circle"></i></a>
        </div>
        <div class="table-responsive ri-table-wrap">
            <table class="table ri-table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">From</th>
                        <th scope="col">To</th>
                        <th scope="col">Journey Date</th>
                        <th scope="col">Searched</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentSearches as $h): ?>
                        <tr>
                            <td><strong><?= esc($h['source']) ?></strong></td>
                            <td><strong><?= esc($h['destination']) ?></strong></td>
                            <td><?= esc($h['date']) ?></td>
                            <td class="text-muted"><?= esc($h['at']) ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm ri-btn-outline"
                                   href="<?= site_url('trains/results') ?>?source=<?= esc($h['source']) ?>&destination=<?= esc($h['destination']) ?>&travel_date=<?= esc($h['date']) ?>">
                                    Search again
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="ri-section">
    <div class="container">
        <h2 class="ri-section__title mb-4">Why RailInfo?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="ri-feature-card">
                    <div class="ri-feature-card__icon"><i class="bi bi-database-fill-check"></i></div>
                    <h5>Cached for Reliability</h5>
                    <p>Schedule and PNR data is cached in our database, so you get fast answers even during peak load.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ri-feature-card">
                    <div class="ri-feature-card__icon"><i class="bi bi-lightning-charge-fill"></i></div>
                    <h5>Instant Station Search</h5>
                    <p>Type-ahead autocomplete finds the right station instantly from over 7,000 stations nationwide.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ri-feature-card">
                    <div class="ri-feature-card__icon"><i class="bi bi-phone-fill"></i></div>
                    <h5>Built for Mobile</h5>
                    <p>A fully responsive layout means schedules, seats, and PNR details are easy to read on any device.</p>
                </div>
            </div>
        </div>
    </div>
</section>
