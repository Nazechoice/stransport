<?php
$pageTitle = 'Services';
$serviceCards = [
    [
        'icon' => 'bi-ticket-perforated',
        'title' => 'Ticketing',
        'copy' => 'Search, reserve, print, and download tickets from a polished passenger journey.',
    ],
    [
        'icon' => 'bi-qr-code-scan',
        'title' => 'QR verification',
        'copy' => 'Ticket officers can scan or manually verify boarding passes at the counter.',
    ],
    [
        'icon' => 'bi-bus-front',
        'title' => 'Fleet control',
        'copy' => 'Manage buses, routes, schedules, and driver assignments from one workspace.',
    ],
    [
        'icon' => 'bi-bar-chart-line',
        'title' => 'Reporting',
        'copy' => 'Daily, weekly, monthly, and revenue reports keep operations visible.',
    ],
];
?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Available services</div>
        <h1 class="dashboard-hero__title">Everything your transport team needs to operate well.</h1>
        <p class="dashboard-hero__copy">
            From passenger bookings to officer verification and admin analytics, the platform keeps each workflow in one place.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('journey/search') ?>">Search journey</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('routes') ?>">View routes</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Operational scope</div>
            <div class="mini-value"><?= e($stats['users']) ?> users</div>
            <div class="mini-note">Passenger, driver, officer, and administrator roles supported.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Bookings <?= e($stats['bookings']) ?></span>
            <span class="badge text-bg-info">Tickets <?= e($stats['tickets']) ?></span>
            <span class="badge text-bg-warning">Pending <?= e($stats['pending_payments']) ?></span>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <?php foreach ($serviceCards as $card): ?>
        <article class="feature-card">
            <i class="bi <?= e($card['icon']) ?> feature-icon"></i>
            <h5><?= e($card['title']) ?></h5>
            <p class="feature-copy mb-0"><?= e($card['copy']) ?></p>
        </article>
    <?php endforeach; ?>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Popular corridors</div>
            <h2 class="page-title mt-1">Routes that power the service layer</h2>
            <p>Popular routes help passengers find the most active travel corridors quickly.</p>
        </div>
    </div>

    <div class="dashboard-grid dashboard-grid--three">
        <?php foreach (array_slice($popularRoutes, 0, 6) as $route): ?>
            <article class="route-card">
                <div class="journey-pill mb-3"><i class="bi bi-arrow-left-right"></i> Popular</div>
                <h3 class="route-title mb-2"><?= e($route['origin']) ?> to <?= e($route['destination']) ?></h3>
                <div class="route-meta"><?= e($route['bookings']) ?> bookings recorded</div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
