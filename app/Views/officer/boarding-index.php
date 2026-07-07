<?php $pageTitle = 'Boarding Lists'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Boarding desk</div>
        <h1 class="dashboard-hero__title">Open boarding lists for today’s departures.</h1>
        <p class="dashboard-hero__copy">
            Choose a trip to view its passenger manifest and boarding list in one click.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('officer/verify') ?>">Verify ticket</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('officer/scanner') ?>">Open scanner</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Today</div>
            <div class="mini-value"><?= e(count($todayTrips)) ?> trips</div>
            <div class="mini-note">Use the list below to open any boarding list.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Boarding list index</div>
            <h2 class="page-title mt-1">Today’s trips</h2>
            <p>Select a trip to see passengers cleared for boarding.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Route</th>
                    <th>Departure</th>
                    <th>Bus</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todayTrips as $trip): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($trip['origin']) ?> to <?= e($trip['destination']) ?></td>
                        <td><?= e($trip['departure_time']) ?></td>
                        <td><?= e($trip['bus_number']) ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-primary" href="<?= url('officer/boarding-list/' . $trip['id']) ?>">Open list</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$todayTrips): ?>
        <div class="empty-state mt-3">
            <i class="bi bi-clipboard-check"></i>
            <h4>No trips scheduled today</h4>
            <p class="text-muted mb-0">Once the administrator schedules a departure, it will appear here.</p>
        </div>
    <?php endif; ?>
</section>
