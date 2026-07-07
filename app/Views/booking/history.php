<?php $pageTitle = 'Booking History'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Journey history</div>
        <h1 class="dashboard-hero__title">Review all booked journeys in one place.</h1>
        <p class="dashboard-hero__copy">
            A cleaner booking history makes it easier to find tickets, routes, and departure details when you need them.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">History</div>
            <div class="mini-value"><?= e(count($bookings)) ?> bookings</div>
            <div class="mini-note">Use your tickets page to open boarding passes for completed trips.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Booking directory</div>
            <h2 class="page-title mt-1">Booking history</h2>
            <p>All your booked journeys in one place.</p>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="<?= url('passenger/tickets') ?>">My tickets</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Booking No</th>
                    <th>Route</th>
                    <th>Seat</th>
                    <th>Departure</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($booking['booking_number']) ?></td>
                        <td><?= e($booking['origin']) ?> to <?= e($booking['destination']) ?></td>
                        <td><span class="badge text-bg-primary"><?= e($booking['seat_number']) ?></span></td>
                        <td><?= e($booking['departure_date']) ?> <?= e($booking['departure_time']) ?></td>
                        <td><span class="badge <?= e(status_badge_class($booking['booking_status'])) ?>"><?= e($booking['booking_status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
