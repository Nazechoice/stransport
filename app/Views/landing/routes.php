<?php
$pageTitle = 'Routes';
$topRoutes = array_slice($popularRoutes, 0, 6);
?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Route listings</div>
        <h1 class="dashboard-hero__title">Explore active routes and fare corridors.</h1>
        <p class="dashboard-hero__copy">
            Review popular connections, compare fares, and jump into the booking flow when you’re ready.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('journey/search') ?>">Search journey</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('services') ?>">View services</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Network overview</div>
            <div class="mini-value"><?= e($stats['routes']) ?> routes</div>
            <div class="mini-note">Active corridors and popular city pairs in one place.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Routes <?= e($stats['routes']) ?></span>
            <span class="badge text-bg-info">Schedules <?= e($stats['schedules']) ?></span>
            <span class="badge text-bg-success">Bookings <?= e($stats['bookings']) ?></span>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--three">
    <?php foreach ($topRoutes as $route): ?>
        <article class="route-card">
            <div class="journey-pill mb-3"><i class="bi bi-signpost-split"></i> Active route</div>
            <h3 class="route-title mb-2"><?= e($route['origin']) ?> to <?= e($route['destination']) ?></h3>
            <div class="route-meta mb-3"><?= e($route['bookings']) ?> bookings on this corridor</div>
            <a class="btn btn-outline-primary btn-sm" href="<?= url('journey/search?origin=' . rawurlencode($route['origin']) . '&destination=' . rawurlencode($route['destination'])) ?>">Book now</a>
        </article>
    <?php endforeach; ?>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">All routes</div>
            <h2 class="page-title mt-1">Available route directory</h2>
            <p>Browse the full list of active route definitions in the system.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Distance</th>
                    <th>Duration</th>
                    <th>Fare</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $route): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($route['origin']) ?></td>
                        <td><?= e($route['destination']) ?></td>
                        <td><?= e($route['distance_km']) ?> km</td>
                        <td><?= e($route['estimated_minutes']) ?> mins</td>
                        <td><?= money($route['fare']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$routes): ?>
        <div class="empty-state mt-3">
            <i class="bi bi-signpost-split"></i>
            <h4>No routes available</h4>
            <p class="text-muted mb-0">Add routes from the administrator dashboard to populate this page.</p>
        </div>
    <?php endif; ?>
</section>
