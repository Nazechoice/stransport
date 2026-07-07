<?php $pageTitle = 'Reports'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Analytics</div>
        <h1 class="dashboard-hero__title">Operational reports in a sharper interface.</h1>
        <p class="dashboard-hero__copy">
            Export monthly insights and review top routes from a cleaner, more professional reporting hub.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('reports') ?>">Open report center</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('reports/export/monthly/pdf') ?>">Export PDF</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Summary</div>
            <div class="mini-value"><?= money($stats['revenue']) ?></div>
            <div class="mini-note">Revenue, passengers, routes, and drivers in one snapshot.</div>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--four">
    <div class="stat-card"><div class="feature-icon"><i class="bi bi-currency-exchange"></i></div><div class="stat-value"><?= money($stats['revenue']) ?></div><div class="stat-label">Revenue</div></div>
    <div class="stat-card"><div class="feature-icon"><i class="bi bi-people"></i></div><div class="stat-value"><?= e($stats['passengers'] ?? $stats['users']) ?></div><div class="stat-label">Passengers</div></div>
    <div class="stat-card"><div class="feature-icon"><i class="bi bi-signpost-split"></i></div><div class="stat-value"><?= e($stats['routes']) ?></div><div class="stat-label">Routes</div></div>
    <div class="stat-card"><div class="feature-icon"><i class="bi bi-truck"></i></div><div class="stat-value"><?= e($stats['drivers'] ?? 0) ?></div><div class="stat-label">Drivers</div></div>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Export center</div>
            <h2 class="page-title mt-1">Monthly exports</h2>
            <p>Monthly export actions and top route performance.</p>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <a class="btn btn-primary" href="<?= url('reports') ?>">Open Report Center</a>
        <a class="btn btn-outline-primary" href="<?= url('reports/export/monthly/csv') ?>">Export Monthly CSV</a>
        <a class="btn btn-outline-primary" href="<?= url('reports/export/monthly/excel') ?>">Export Monthly Excel</a>
        <a class="btn btn-outline-primary" href="<?= url('reports/export/monthly/pdf') ?>">Export Monthly PDF</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Route</th><th>Bookings</th></tr></thead>
            <tbody>
                <?php foreach ($summary['top_routes'] as $route): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($route['origin']) ?> to <?= e($route['destination']) ?></td>
                        <td><span class="badge text-bg-primary"><?= e($route['bookings']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
