<section class="page-heading mb-4">
    <div>
        <div class="mini-label">Available trips</div>
        <h1 class="page-title mt-1">Trip results</h1>
        <p>Matching trips for your selected route and date.</p>
    </div>
</section>

<section class="panel-card mb-4">
    <form class="row g-3 align-items-end" method="get" action="<?= url('journey/search') ?>">
        <div class="col-md-4">
            <label class="form-label">Origin</label>
            <input name="origin" value="<?= e($origin) ?>" class="form-control" placeholder="Origin">
        </div>
        <div class="col-md-4">
            <label class="form-label">Destination</label>
            <input name="destination" value="<?= e($destination) ?>" class="form-control" placeholder="Destination">
        </div>
        <div class="col-md-3">
            <label class="form-label">Travel date</label>
            <input type="date" name="date" value="<?= e($date) ?>" class="form-control">
        </div>
        <?php if (!empty($busId)): ?>
            <input type="hidden" name="bus_id" value="<?= (int) $busId ?>">
        <?php endif; ?>
        <?php if (!empty($routeId)): ?>
            <input type="hidden" name="route_id" value="<?= (int) $routeId ?>">
        <?php endif; ?>
        <div class="col-md-1">
            <button class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
        </div>
    </form>
</section>

<?php if ($schedules): ?>
    <div class="dashboard-grid dashboard-grid--two">
        <?php foreach ($schedules as $schedule): ?>
            <div class="journey-card">
                <?php if (!empty($schedule['image'])): ?>
                    <img src="<?= url($schedule['image']) ?>" alt="<?= e($schedule['bus_number']) ?>" class="rounded-4 mb-3 w-100" style="height: 180px; object-fit: cover;">
                <?php else: ?>
                    <div class="empty-state mb-3" style="min-height: 180px;">
                        <i class="bi bi-bus-front"></i>
                        <h4>No image available</h4>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="journey-pill mb-3"><i class="bi bi-bus-front"></i> <?= e($schedule['status'] ?? 'scheduled') ?></div>
                        <h3 class="route-title mb-2"><?= e($schedule['origin']) ?> to <?= e($schedule['destination']) ?></h3>
                        <div class="journey-meta"><?= e($schedule['bus_number']) ?> | <?= e($schedule['bus_type']) ?></div>
                        <div class="small text-muted"><?= e($schedule['registration_number'] ?? '') ?></div>
                    </div>
                    <div class="text-end">
                        <div class="stat-value fs-4"><?= money($schedule['fare']) ?></div>
                        <div class="text-muted small"><?= e($schedule['available_seats']) ?> seats available</div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge text-bg-light border">Departure: <?= e($schedule['departure_date']) ?> <?= e($schedule['departure_time']) ?></span>
                    <span class="badge text-bg-light border">Arrival: <?= e($schedule['arrival_time']) ?></span>
                    <span class="badge text-bg-light border">Duration: <?= e($schedule['estimated_minutes']) ?> mins</span>
                    <span class="badge text-bg-light border">Capacity: <?= e($schedule['capacity']) ?></span>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= url('booking/details/' . $schedule['id']) ?>" class="btn btn-outline-primary">View Details</a>
                    <a href="<?= url('booking/' . $schedule['id']) ?>" class="btn btn-primary" aria-label="Select Seat">Book Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <?php if (!empty($stats['schedules'])): ?>
        <div class="panel-card text-center py-5">
            <i class="bi bi-search fs-1 text-primary"></i>
            <h4 class="mt-3 mb-2">No exact matches found</h4>
            <p class="text-muted mb-0">Try a different date or route, or choose one of the upcoming trips below.</p>
        </div>
    <?php else: ?>
        <div class="panel-card text-center py-5">
            <i class="bi bi-database fs-1 text-primary"></i>
            <h4 class="mt-3 mb-2">No trips have been scheduled yet</h4>
            <p class="text-muted mb-0">Add routes, buses, and schedules from the administrator dashboard, or run the demo seeder to create sample trips.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($featuredTrips)): ?>
        <div class="mt-4">
            <div class="section-heading mb-3">
                <div>
                    <div class="mini-label">Suggested trips</div>
                    <h2 class="page-title mt-1">You can still book these</h2>
                </div>
            </div>
            <div class="dashboard-grid dashboard-grid--three">
                <?php foreach ($featuredTrips as $trip): ?>
                    <article class="journey-card">
                        <?php if (!empty($trip['image'])): ?>
                            <img src="<?= url($trip['image']) ?>" alt="<?= e($trip['bus_number']) ?>" class="rounded-4 mb-3 w-100" style="height: 180px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="journey-pill mb-3"><i class="bi bi-bus-front"></i> <?= e($trip['status'] ?? 'scheduled') ?></div>
                        <h3 class="route-title mb-2"><?= e($trip['origin']) ?> to <?= e($trip['destination']) ?></h3>
                        <div class="journey-meta mb-2"><?= e($trip['bus_number']) ?> | <?= e($trip['bus_type']) ?></div>
                        <div class="small text-muted mb-3"><?= e($trip['departure_date']) ?> <?= e($trip['departure_time']) ?></div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= url('booking/details/' . $trip['id']) ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                            <a href="<?= url('booking/' . $trip['id']) ?>" class="btn btn-primary btn-sm" aria-label="Select Seat">Book Now</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
