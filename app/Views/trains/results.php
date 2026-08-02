<section class="ri-page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb ri-breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('trains') ?>">Train Schedule</a></li>
                <li class="breadcrumb-item active">Results</li>
            </ol>
        </nav>
        <h1>
            <?= esc($source) ?> <i class="bi bi-arrow-right mx-2"></i> <?= esc($destination) ?>
        </h1>
        <p><i class="bi bi-calendar-event me-2"></i><?= esc(date('D, d M Y', strtotime($travel_date))) ?>
            <?php if ($fromCache): ?>
                <span class="badge ri-badge-cache ms-2"><i class="bi bi-hdd-fill me-1"></i>Cached Data</span>
            <?php endif; ?>
        </p>
    </div>
</section>

<section class="ri-section">
    <div class="container">

        <?php if (empty($results)): ?>
            <div class="ri-empty-state">
                <i class="bi bi-emoji-frown"></i>
                <h4>No trains found for this route on the selected date</h4>
                <p class="text-muted">Try a different date, or double-check the station names/codes.</p>
                <a href="<?= site_url('trains') ?>" class="btn ri-btn-primary mt-2">
                    <i class="bi bi-arrow-left me-2"></i>Modify Search
                </a>
            </div>
        <?php else: ?>

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <span class="text-muted"><?= count($results) ?> train(s) found</span>
                <a href="<?= site_url('trains') ?>" class="ri-link-more"><i class="bi bi-arrow-left"></i> New Search</a>
            </div>

            <!-- Card view (article per train) for quick scanning -->
            <div class="row g-3 mb-4 d-lg-none">
                <?php foreach ($results as $row): ?>
                    <div class="col-12">
                        <article class="ri-train-card">
                            <header class="ri-train-card__head">
                                <div>
                                    <h6 class="mb-0"><?= esc($row['train_name']) ?></h6>
                                    <small class="text-muted">#<?= esc($row['train_number']) ?></small>
                                </div>
                                <?php
                                    $statusMap = [
                                        'ON_TIME' => ['success', 'On Time'],
                                        'DELAYED' => ['warning', 'Delayed'],
                                        'CANCELLED' => ['danger', 'Cancelled'],
                                    ];
                                    [$statusClass, $statusLabel] = $statusMap[$row['status']] ?? ['secondary', 'Scheduled'];
                                ?>
                                <span class="badge ri-badge-status ri-badge-status--<?= $statusClass ?>"><?= $statusLabel ?></span>
                            </header>
                            <div class="ri-train-card__times">
                                <div><strong><?= esc($row['departure_time']) ?></strong><span><?= esc($source) ?></span></div>
                                <div class="ri-train-card__duration"><i class="bi bi-arrow-right"></i><small><?= esc($row['duration'] ?? '—') ?></small></div>
                                <div><strong><?= esc($row['arrival_time']) ?></strong><span><?= esc($destination) ?></span></div>
                            </div>
                            <div class="ri-seat-grid">
                                <?php foreach ([['SL', $row['sl_seats'], $row['sl_fare']], ['3A', $row['ac3_seats'], $row['ac3_fare']], ['2A', $row['ac2_seats'], $row['ac2_fare']], ['1A', $row['ac1_seats'], $row['ac1_fare']]] as [$cls, $seats, $fare]): ?>
                                    <?php if ($seats !== null): ?>
                                        <div class="ri-seat-chip <?= $seats > 20 ? 'ri-seat-chip--good' : ($seats > 0 ? 'ri-seat-chip--low' : 'ri-seat-chip--full') ?>">
                                            <span class="ri-seat-chip__class"><?= $cls ?></span>
                                            <span class="ri-seat-chip__count"><?= $seats > 0 ? $seats . ' avl' : 'WL' ?></span>
                                            <span class="ri-seat-chip__fare">₹<?= number_format((float) $fare) ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <a href="<?= site_url('trains/' . $row['train_number']) ?>" class="stretched-link"></a>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Table view for desktop; horizontally scrollable on smaller screens -->
            <div class="table-responsive ri-table-wrap d-none d-lg-block">
                <table class="table ri-table align-middle mb-0">
                    <caption class="visually-hidden">Train schedule results from <?= esc($source) ?> to <?= esc($destination) ?></caption>
                    <thead>
                        <tr>
                            <th scope="col">Train</th>
                            <th scope="col">Departure</th>
                            <th scope="col">Arrival</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Sleeper</th>
                            <th scope="col">AC 3-Tier</th>
                            <th scope="col">AC 2-Tier</th>
                            <th scope="col">AC 1st Class</th>
                            <th scope="col">Status</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                            <?php
                                $statusMap = [
                                    'ON_TIME' => ['success', 'On Time'],
                                    'DELAYED' => ['warning', 'Delayed'],
                                    'CANCELLED' => ['danger', 'Cancelled'],
                                ];
                                [$statusClass, $statusLabel] = $statusMap[$row['status']] ?? ['secondary', 'Scheduled'];
                            ?>
                            <tr>
                                <td>
                                    <strong><?= esc($row['train_name']) ?></strong><br>
                                    <small class="text-muted">#<?= esc($row['train_number']) ?> &middot; <?= esc($row['train_type']) ?></small>
                                </td>
                                <td><?= esc($row['departure_time']) ?></td>
                                <td><?= esc($row['arrival_time']) ?></td>
                                <td><?= esc($row['duration'] ?? '—') ?></td>
                                <?php foreach ([[$row['sl_seats'], $row['sl_fare']], [$row['ac3_seats'], $row['ac3_fare']], [$row['ac2_seats'], $row['ac2_fare']], [$row['ac1_seats'], $row['ac1_fare']]] as [$seats, $fare]): ?>
                                    <td>
                                        <?php if ($seats === null): ?>
                                            <span class="text-muted">—</span>
                                        <?php else: ?>
                                            <span class="ri-seat-pill <?= $seats > 20 ? 'ri-seat-pill--good' : ($seats > 0 ? 'ri-seat-pill--low' : 'ri-seat-pill--full') ?>">
                                                <?= $seats > 0 ? $seats . ' Available' : 'Waitlist' ?>
                                            </span>
                                            <div class="small text-muted">₹<?= number_format((float) $fare) ?></div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td><span class="badge ri-badge-status ri-badge-status--<?= $statusClass ?>"><?= $statusLabel ?></span></td>
                                <td><a href="<?= site_url('trains/' . $row['train_number']) ?>" class="btn btn-sm ri-btn-outline">Details</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>
</section>
