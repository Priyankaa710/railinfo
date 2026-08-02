<section class="ri-page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb ri-breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('trains') ?>">Train Schedule</a></li>
                <li class="breadcrumb-item active">Train Details</li>
            </ol>
        </nav>
        <h1><?= $train ? esc($train['train_name']) : 'Train Not Found' ?></h1>
    </div>
</section>

<section class="ri-section">
    <div class="container">
        <?php if (! $train): ?>
            <div class="ri-empty-state">
                <i class="bi bi-exclamation-triangle"></i>
                <h4>We couldn't find that train number</h4>
                <a href="<?= site_url('trains') ?>" class="btn ri-btn-primary mt-2">Back to search</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card ri-card p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                            <div>
                                <h4 class="mb-0"><?= esc($train['train_name']) ?></h4>
                                <span class="text-muted">Train No. <?= esc($train['train_number']) ?> &middot; <?= esc($train['train_type']) ?></span>
                            </div>
                            <span class="badge ri-badge-cache"><i class="bi bi-hdd-fill me-1"></i>Cached</span>
                        </div>
                        <div class="ri-train-card__times ri-train-card__times--lg">
                            <div><strong><?= esc($train['departure_time']) ?></strong><span><?= esc($train['source_code']) ?></span></div>
                            <div class="ri-train-card__duration"><i class="bi bi-arrow-right"></i><small><?= esc($train['duration'] ?? '—') ?></small></div>
                            <div><strong><?= esc($train['arrival_time']) ?></strong><span><?= esc($train['destination_code']) ?></span></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card ri-card p-4">
                        <h6 class="mb-3"><i class="bi bi-calendar-week me-2"></i>Runs On</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($days as $day => $runs): ?>
                                <span class="badge <?= $runs ? 'ri-badge-day-active' : 'ri-badge-day-inactive' ?>"><?= $day ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
