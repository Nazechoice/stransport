<?php
$pageTitle = 'About';
$topRoutes = array_slice($popularRoutes, 0, 3);
?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">About the platform</div>
        <h1 class="dashboard-hero__title">A professional transport system built for real operations.</h1>
        <p class="dashboard-hero__copy">
            Public Bus Transport Ticketing System centralizes passenger booking, ticket issuance, officer verification,
            driver manifests, and administration into one modern workspace.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('journey/search') ?>">Search journey</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('register') ?>">Create account</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">System scope</div>
            <div class="mini-value"><?= e($stats['users']) ?> users</div>
            <div class="mini-note">Built for passengers, officers, drivers, and administrators.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Bookings <?= e($stats['bookings']) ?></span>
            <span class="badge text-bg-info">Tickets <?= e($stats['tickets']) ?></span>
            <span class="badge text-bg-warning">Routes <?= e($stats['routes']) ?></span>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--three">
    <article class="feature-card">
        <i class="bi bi-shield-check feature-icon"></i>
        <h5>Secure access</h5>
        <p class="feature-copy mb-0">Role-based authentication, CSRF protection, password hashing, and session handling.</p>
    </article>
    <article class="feature-card">
        <i class="bi bi-ticket-perforated feature-icon"></i>
        <h5>Digital ticketing</h5>
        <p class="feature-copy mb-0">Passengers can reserve seats, receive QR tickets, print receipts, and track travel history.</p>
    </article>
    <article class="feature-card">
        <i class="bi bi-graph-up-arrow feature-icon"></i>
        <h5>Operational insight</h5>
        <p class="feature-copy mb-0">Administrators can monitor bookings, payments, logs, and route performance from one dashboard.</p>
    </article>
</section>

<section class="landing-section">
    <div class="dashboard-grid dashboard-grid--two">
        <div class="panel-card">
            <div class="section-heading mb-3">
                <div>
                    <div class="mini-label">How it works</div>
                    <h2 class="page-title mt-1">A workflow that mirrors a real transport desk</h2>
                    <p>Each screen supports an actual operational step, not a placeholder demo flow.</p>
                </div>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div>
                        <h6>Passengers search journeys</h6>
                        <p>Routes, schedules, fares, and seat availability are presented before booking.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div>
                        <h6>Staff validate tickets</h6>
                        <p>Ticket officers can verify QR tickets and mark boarding activity as used.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div>
                        <h6>Admins manage operations</h6>
                        <p>Users, buses, routes, schedules, messages, reports, and settings stay organized in one system.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="section-heading mb-3">
                <div>
                    <div class="mini-label">Popular routes</div>
                    <h2 class="page-title mt-1">Real corridors already in the system</h2>
                    <p>These sample corridors power the booking and reporting workflows used in the demo data.</p>
                </div>
            </div>

            <div class="dashboard-grid dashboard-grid--one">
                <?php foreach ($topRoutes as $route): ?>
                    <article class="route-card">
                        <div class="journey-pill mb-3"><i class="bi bi-signpost-split"></i> Active route</div>
                        <h3 class="route-title mb-2"><?= e($route['origin']) ?> to <?= e($route['destination']) ?></h3>
                        <div class="route-meta"><?= e($route['bookings']) ?> bookings recorded</div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="landing-section pt-0">
    <div class="panel-card d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="mini-label">Ready for defence</div>
            <h2 class="page-title mt-1 mb-2">Built to demonstrate a complete final-year project workflow.</h2>
            <p class="mb-0 text-muted">Use this page as a clean introduction before showing booking, dashboards, and verification screens.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= url('routes') ?>" class="btn btn-outline-primary btn-lg">View routes</a>
            <a href="<?= url('services') ?>" class="btn btn-primary btn-lg">View services</a>
        </div>
    </div>
</section>
