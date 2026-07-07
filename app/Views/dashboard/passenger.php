<?php $pageTitle = 'Passenger Dashboard'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Your travel hub</div>
        <h1 class="dashboard-hero__title">Plan, book, and travel with confidence.</h1>
        <p class="dashboard-hero__copy">
            Keep track of bookings, tickets, upcoming trips, and notifications from one calm, modern workspace.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('journey/search') ?>">Search journeys</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('bookings/history') ?>">Booking history</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('passenger/tickets') ?>">My tickets</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Travel summary</div>
            <div class="mini-value"><?= e(count($upcomingTrips)) ?> upcoming trips</div>
            <div class="mini-note"><?= e(count($notifications)) ?> notifications waiting for your attention.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Bookings <?= e($stats['bookings']) ?></span>
            <span class="badge text-bg-info">Tickets <?= e($stats['tickets']) ?></span>
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
        <div class="feature-icon"><i class="bi bi-truck"></i></div>
        <div class="stat-value"><?= count($upcomingTrips) ?></div>
        <div class="stat-label">Upcoming trips</div>
    </div>
    <div class="stat-card">
        <div class="feature-icon"><i class="bi bi-bell"></i></div>
        <div class="stat-value"><?= count($notifications) ?></div>
        <div class="stat-label">Notifications</div>
    </div>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Upcoming trips</div>
                <h2 class="page-title mt-1">Your next departures</h2>
                <p>Trips are sorted by departure date and time.</p>
            </div>
            <a class="btn btn-outline-primary btn-sm" href="<?= url('journey/search') ?>">Book now</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Date</th>
                        <th>Bus</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingTrips as $trip): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($trip['origin']) ?> to <?= e($trip['destination']) ?></td>
                            <td><?= e($trip['departure_date']) ?> <?= e($trip['departure_time']) ?></td>
                            <td><?= e($trip['bus_number']) ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="<?= url('booking/' . $trip['id']) ?>">Book</a>
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
                <div class="mini-label">Notifications</div>
                <h2 class="page-title mt-1">Travel alerts</h2>
                <p>Booking updates, reminders, and account notifications.</p>
            </div>
        </div>

        <div class="timeline">
            <?php foreach ($notifications as $notification): ?>
                <div class="timeline-item">
                    <div>
                        <h6><?= e($notification['title'] ?? 'Notification') ?></h6>
                        <p><?= e($notification['message']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$notifications): ?>
            <div class="empty-state mt-3">
                <i class="bi bi-bell"></i>
                <h4>No notifications yet</h4>
                <p class="text-muted mb-0">Booking updates and travel alerts will appear here.</p>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a class="btn btn-primary" href="<?= url('bookings/history') ?>">View History</a>
        </div>
    </div>
</section>
