<?php $pageTitle = 'Trip Details'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Trip preview</div>
        <h1 class="dashboard-hero__title"><?= e($schedule['origin']) ?> to <?= e($schedule['destination']) ?></h1>
        <p class="dashboard-hero__copy">
            Review the coach, driver, travel time, seat availability, and fare before you continue to seat selection.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('booking/' . $schedule['id']) ?>">Book Now</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('journey/search') ?>">Back to search</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Availability</div>
            <div class="mini-value"><?= e($availableSeats) ?> seats</div>
            <div class="mini-note"><?= e($bookedCount) ?> seats already reserved on this trip.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Fare <?= money($schedule['fare']) ?></span>
            <span class="badge text-bg-info">Bus <?= e($schedule['bus_number']) ?></span>
            <span class="badge text-bg-success"><?= e($schedule['status']) ?></span>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <?php if (!empty($schedule['image'])): ?>
            <img src="<?= url($schedule['image']) ?>" alt="<?= e($schedule['bus_number']) ?>" class="rounded-4 mb-4 w-100" style="height: 280px; object-fit: cover;">
        <?php else: ?>
            <div class="empty-state mb-4" style="min-height: 280px;">
                <i class="bi bi-bus-front"></i>
                <h4>No bus image available</h4>
            </div>
        <?php endif; ?>

        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Bus details</div>
                <h2 class="page-title mt-1">Vehicle and schedule information</h2>
                <p>Everything shown here is pulled from the live database record for this schedule.</p>
            </div>
        </div>

        <div class="ticket-meta-grid">
            <div><span>Bus number</span><strong><?= e($schedule['bus_number']) ?></strong></div>
            <div><span>Registration</span><strong><?= e($schedule['registration_number']) ?></strong></div>
            <div><span>Bus type</span><strong><?= e($schedule['bus_type']) ?></strong></div>
            <div><span>Capacity</span><strong><?= e($schedule['capacity']) ?></strong></div>
            <div><span>Departure</span><strong><?= e($schedule['departure_date']) ?> <?= e($schedule['departure_time']) ?></strong></div>
            <div><span>Arrival</span><strong><?= e($schedule['arrival_time']) ?></strong></div>
        </div>
    </div>

    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Route overview</div>
                <h2 class="page-title mt-1"><?= e($schedule['origin']) ?> to <?= e($schedule['destination']) ?></h2>
                <p><?= e($schedule['estimated_minutes']) ?> minutes estimated travel time.</p>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="badge text-bg-light border">Distance: <?= e($schedule['distance_km']) ?> km</span>
            <span class="badge text-bg-light border">Fare: <?= money($schedule['route_fare']) ?></span>
            <span class="badge text-bg-light border">Available seats: <?= e($availableSeats) ?></span>
            <span class="badge text-bg-light border">Status: <?= e($schedule['status']) ?></span>
        </div>

        <div class="mb-4">
            <h5 class="mb-3">Amenities</h5>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($amenities as $amenity): ?>
                    <span class="badge text-bg-primary-subtle text-primary border border-primary-subtle"><?= e($amenity) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($schedule['stops'])): ?>
            <div class="mb-4">
                <h5 class="mb-2">Route stops</h5>
                <p class="text-muted mb-0"><?= e($schedule['stops']) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($schedule['driver_name'])): ?>
            <div class="mb-4">
                <h5 class="mb-2">Driver</h5>
                <p class="text-muted mb-0"><?= e($schedule['driver_name']) ?><?= !empty($schedule['driver_phone']) ? ' • ' . e($schedule['driver_phone']) : '' ?></p>
            </div>
        <?php endif; ?>

        <div class="ticket-actions">
            <a class="btn btn-primary" href="<?= url('booking/' . $schedule['id']) ?>">Continue to Seat Selection</a>
            <a class="btn btn-outline-primary" href="<?= url('journey/search?route_id=' . (int) ($schedule['route_id'] ?? 0)) ?>">Find Similar Trips</a>
        </div>
    </div>
</section>
