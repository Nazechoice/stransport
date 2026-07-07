<?php $pageTitle = 'Officer Dashboard'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Boarding control</div>
        <h1 class="dashboard-hero__title">Verify tickets and board passengers faster.</h1>
        <p class="dashboard-hero__copy">
            Use the officer workspace to scan tickets, handle walk-ins, and prepare for today's departures.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('officer/verify') ?>">Verify ticket</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('officer/scanner') ?>">Open scanner</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('officer/walkin') ?>">Walk-in booking</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Boarding desk</div>
            <div class="mini-value"><?= count($todayTrips) ?> trips scheduled</div>
            <div class="mini-note">Keep boarding smooth by checking tickets before departure time.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Bookings <?= e($stats['bookings']) ?></span>
            <span class="badge text-bg-info">Tickets <?= e($stats['tickets']) ?></span>
            <span class="badge text-bg-warning">Pending <?= e($stats['pending_payments']) ?></span>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--four">
    <div class="stat-card">
        <div class="feature-icon"><i class="bi bi-receipt"></i></div>
        <div class="stat-value"><?= e($stats['bookings']) ?></div>
        <div class="stat-label">Bookings</div>
    </div>
    <div class="stat-card">
        <div class="feature-icon"><i class="bi bi-ticket-perforated"></i></div>
        <div class="stat-value"><?= e($stats['tickets']) ?></div>
        <div class="stat-label">Tickets</div>
    </div>
    <div class="stat-card">
        <div class="feature-icon"><i class="bi bi-clock"></i></div>
        <div class="stat-value"><?= e($stats['pending_payments']) ?></div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="feature-icon"><i class="bi bi-bus-front"></i></div>
        <div class="stat-value"><?= count($todayTrips) ?></div>
        <div class="stat-label">Today's trips</div>
    </div>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Today's trips</div>
                <h2 class="page-title mt-1">Trips ready for boarding</h2>
                <p>Departures scheduled for today and ready for verification.</p>
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
                                <a class="btn btn-sm btn-outline-primary" href="<?= url('officer/boarding-list/' . $trip['id']) ?>">Boarding list</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Quick actions</div>
                <h2 class="page-title mt-1">Counter shortcuts</h2>
                <p>Fast links for the busiest boarding desk tasks.</p>
            </div>
        </div>
        <div class="wizard">
            <a href="<?= url('officer/verify') ?>" class="wizard-step text-decoration-none">
                <div class="wizard-step__index">1</div>
                <div>
                    <strong>Verify ticket</strong>
                    <div class="wizard-step__copy">Look up a ticket number instantly.</div>
                </div>
            </a>
            <a href="<?= url('officer/scanner') ?>" class="wizard-step text-decoration-none">
                <div class="wizard-step__index">2</div>
                <div>
                    <strong>Open scanner</strong>
                    <div class="wizard-step__copy">Use the camera to scan QR codes.</div>
                </div>
            </a>
            <a href="<?= url('officer/walkin') ?>" class="wizard-step text-decoration-none">
                <div class="wizard-step__index">3</div>
                <div>
                    <strong>Create walk-in booking</strong>
                    <div class="wizard-step__copy">Issue a booking at the counter.</div>
                </div>
            </a>
        </div>
    </div>
</section>
