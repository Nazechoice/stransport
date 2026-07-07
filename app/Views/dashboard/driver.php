<?php $pageTitle = 'Driver Dashboard'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Driver workspace</div>
        <h1 class="dashboard-hero__title">Stay on top of trips and manifests.</h1>
        <p class="dashboard-hero__copy">
            Review departures, open manifests, and complete trips from a focused dashboard built for drivers.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('driver/trips') ?>">Assigned trips</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('driver/trips') ?>">Open manifest</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Trip overview</div>
            <div class="mini-value"><?= e(count($trips)) ?> assigned trips</div>
            <div class="mini-note">Use the list below to jump into trip execution quickly.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Trips <?= e(count($trips)) ?></span>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Assigned trips</div>
            <h2 class="page-title mt-1">Trip roster</h2>
            <p>Upcoming and historical trips mapped to your account.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Route</th>
                    <th>Departure</th>
                    <th>Bus</th>
                    <th>Bookings</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($trip['origin']) ?> to <?= e($trip['destination']) ?></td>
                        <td><?= e($trip['departure_date']) ?> <?= e($trip['departure_time']) ?></td>
                        <td><?= e($trip['bus_number']) ?></td>
                        <td><span class="badge text-bg-primary"><?= e($trip['bookings']) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= url('driver/manifest/' . $trip['id']) ?>">Manifest</a>
                            <form action="<?= url('driver/complete-trip/' . $trip['id']) ?>" method="post" class="d-inline">
                                <?= \Transport\Core\Csrf::field() ?>
                                <button class="btn btn-sm btn-primary">Complete trip</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$trips): ?>
        <div class="empty-state mt-3">
            <i class="bi bi-truck"></i>
            <h4>No trips assigned</h4>
            <p class="text-muted mb-0">Trips will appear here once the administrator schedules them.</p>
        </div>
    <?php endif; ?>
</section>
