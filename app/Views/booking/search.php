<?php $pageTitle = 'Search Journey'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Trip search</div>
        <h1 class="dashboard-hero__title">Find routes and schedules faster.</h1>
        <p class="dashboard-hero__copy">
            Search available journeys with a cleaner booking experience and a premium card layout.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('register') ?>">Create account</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('login') ?>">Login</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Travel planner</div>
            <div class="mini-value">Journey search</div>
            <div class="mini-note">Enter origin, destination, and date to discover available trips.</div>
        </div>
        <div class="timeline">
            <div class="timeline-item">
                <div>
                    <h6>1. Search</h6>
                    <p>Choose the route and departure date.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div>
                    <h6>2. Select seat</h6>
                    <p>Pick a seat from the seat map.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div>
                    <h6>3. Confirm</h6>
                    <p>Complete booking and receive a ticket.</p>
                </div>
            </div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Search journey</div>
            <h2 class="page-title mt-1">Trip finder</h2>
            <p>Find routes and schedules for your next trip.</p>
        </div>
    </div>
    <form class="row g-3" method="get" action="<?= url('journey/search') ?>">
        <div class="col-md-4">
            <label class="form-label">Origin</label>
            <input name="origin" value="<?= e($origin ?? '') ?>" class="form-control form-control-lg" placeholder="e.g. Lagos">
        </div>
        <div class="col-md-4">
            <label class="form-label">Destination</label>
            <input name="destination" value="<?= e($destination ?? '') ?>" class="form-control form-control-lg" placeholder="e.g. Abuja">
        </div>
        <div class="col-md-4">
            <label class="form-label">Travel date</label>
            <input type="date" name="date" value="<?= e($date ?? '') ?>" class="form-control form-control-lg">
        </div>
        <?php if (!empty($busId)): ?>
            <input type="hidden" name="bus_id" value="<?= (int) $busId ?>">
        <?php endif; ?>
        <?php if (!empty($routeId)): ?>
            <input type="hidden" name="route_id" value="<?= (int) $routeId ?>">
        <?php endif; ?>
        <div class="col-12">
            <button class="btn btn-primary btn-lg">Search</button>
        </div>
    </form>
</section>

<section class="mt-4">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Upcoming trips</div>
            <h2 class="page-title mt-1">Ready to book now</h2>
            <p>These are live schedules pulled from the database when no filters are applied.</p>
        </div>
    </div>

    <?php if (!empty($featuredTrips)): ?>
        <div class="dashboard-grid dashboard-grid--three">
            <?php foreach ($featuredTrips as $trip): ?>
                <article class="journey-card">
                    <?php if (!empty($trip['image'])): ?>
                        <img src="<?= url($trip['image']) ?>" alt="<?= e($trip['bus_number']) ?>" class="rounded-4 mb-3 w-100" style="height: 180px; object-fit: cover;">
                    <?php else: ?>
                        <div class="empty-state mb-3" style="min-height: 180px;">
                            <i class="bi bi-bus-front"></i>
                            <h4>No image available</h4>
                        </div>
                    <?php endif; ?>

                    <div class="journey-pill mb-3"><i class="bi bi-bus-front"></i> <?= e($trip['status'] ?? 'scheduled') ?></div>
                    <h3 class="route-title mb-2"><?= e($trip['origin']) ?> to <?= e($trip['destination']) ?></h3>
                    <div class="route-meta mb-2"><?= e($trip['bus_number']) ?> | <?= e($trip['bus_type']) ?></div>
                    <div class="small text-muted mb-3"><?= e($trip['registration_number'] ?? '') ?></div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-light border">Departure: <?= e($trip['departure_date']) ?> <?= e($trip['departure_time']) ?></span>
                        <span class="badge text-bg-light border">Arrival: <?= e($trip['arrival_time']) ?></span>
                        <span class="badge text-bg-light border">Seats: <?= e($trip['available_seats']) ?></span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= url('booking/details/' . $trip['id']) ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                        <a href="<?= url('booking/' . $trip['id']) ?>" class="btn btn-primary btn-sm">Book Now</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="panel-card text-center py-5">
            <i class="bi bi-search fs-1 text-primary"></i>
            <h4 class="mt-3 mb-2">No trips have been scheduled yet</h4>
            <p class="text-muted mb-0">The admin can add routes, buses, and schedules from the dashboard, or you can run the demo seeder to populate test trips.</p>
        </div>
    <?php endif; ?>
</section>
