<section class="ri-page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb ri-breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('pnr') ?>">PNR Status</a></li>
                <li class="breadcrumb-item active">Result</li>
            </ol>
        </nav>
        <h1>PNR <?= esc($pnr) ?></h1>
    </div>
</section>

<section class="ri-section">
    <div class="container">
        <?php if ($notFound): ?>
            <div class="ri-empty-state">
                <i class="bi bi-emoji-frown"></i>
                <h4>No details found for this PNR</h4>
                <p class="text-muted">Please double-check the number, or try again in a few minutes.</p>
                <a href="<?= site_url('pnr') ?>" class="btn ri-btn-primary mt-2"><i class="bi bi-arrow-left me-2"></i>Try Another PNR</a>
            </div>
        <?php else: ?>
            <?php
                $statusMap = [
                    'CNF'     => ['success', 'Confirmed'],
                    'RAC'     => ['warning', 'RAC'],
                    'WL'      => ['danger', 'Waitlisted'],
                    'UNKNOWN' => ['secondary', 'Unknown'],
                ];
                [$statusClass, $statusLabel] = $statusMap[$record['current_status']] ?? ['secondary', $record['current_status']];
            ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="card ri-card p-4 p-md-5 ri-pnr-card">
                        <header class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                            <div>
                                <h4 class="mb-0"><?= esc($record['train_name'] ?? 'Train') ?></h4>
                                <span class="text-muted">Train No. <?= esc($record['train_number'] ?? '—') ?></span>
                            </div>
                            <span class="badge ri-badge-status ri-badge-status--<?= $statusClass ?> fs-6"><?= $statusLabel ?></span>
                        </header>

                        <div class="row g-3 ri-pnr-meta mb-4">
                            <div class="col-6 col-md-3">
                                <span class="ri-pnr-meta__label">Journey Date</span>
                                <span class="ri-pnr-meta__value"><?= esc($record['journey_date'] ?? '—') ?></span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="ri-pnr-meta__label">From</span>
                                <span class="ri-pnr-meta__value"><?= esc($record['source_code'] ?? '—') ?></span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="ri-pnr-meta__label">To</span>
                                <span class="ri-pnr-meta__value"><?= esc($record['destination_code'] ?? '—') ?></span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="ri-pnr-meta__label">Class</span>
                                <span class="ri-pnr-meta__value"><?= esc($record['class'] ?? '—') ?></span>
                            </div>
                        </div>

                        <h6 class="mb-3"><i class="bi bi-people-fill me-2"></i>Passenger Details</h6>
                        <div class="table-responsive ri-table-wrap">
                            <table class="table ri-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Booking Status</th>
                                        <th scope="col">Current Status</th>
                                        <th scope="col">Coach / Berth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (! empty($record['passengers'])): ?>
                                        <?php foreach ($record['passengers'] as $i => $p): ?>
                                            <tr>
                                                <td>Passenger <?= $i + 1 ?></td>
                                                <td><?= esc($p['booking_status'] ?? '—') ?></td>
                                                <td><?= esc($p['current_status'] ?? '—') ?></td>
                                                <td><?= esc($p['coach'] ?? '—') ?> / <?= esc($p['berth'] ?? '—') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center text-muted py-3">No passenger-level detail cached yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </article>
                </div>

                <div class="col-lg-4">
                    <div class="card ri-card p-4">
                        <h6 class="mb-3"><i class="bi bi-clipboard-check me-2"></i>Chart Status</h6>
                        <?php if (! empty($record['chart_prepared'])): ?>
                            <div class="alert alert-success ri-alert mb-0"><i class="bi bi-check-circle-fill me-2"></i>Chart has been prepared.</div>
                        <?php else: ?>
                            <div class="alert alert-warning ri-alert mb-0"><i class="bi bi-hourglass-split me-2"></i>Chart not prepared yet.</div>
                        <?php endif; ?>
                        <p class="text-muted small mt-3 mb-0">
                            Boarding point: <strong><?= esc($record['boarding_point'] ?? '—') ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
