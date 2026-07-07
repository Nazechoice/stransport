<?php $pageTitle = 'Bookings'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Reservations</div>
        <h1 class="dashboard-hero__title">Review bookings in a more readable workspace.</h1>
        <p class="dashboard-hero__copy">
            Search, filter, and monitor confirmed and pending bookings with less visual noise.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('admin/reports') ?>">Open reports</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Booking list</div>
            <div class="mini-value"><?= e(count($bookings)) ?> records</div>
            <div class="mini-note">Use the search bar below to narrow down results quickly.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-success">Confirmed</span>
            <span class="badge text-bg-warning">Pending</span>
            <span class="badge text-bg-danger">Cancelled</span>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Booking directory</div>
            <h2 class="page-title mt-1">Bookings</h2>
            <p>All confirmed and pending bookings in one list.</p>
        </div>
    </div>

    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#bookingsTable" placeholder="Search bookings by booking number, passenger, or route">
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="bookingsTable">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Passenger</th>
                    <th>Route</th>
                    <th>Seat</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Ticket</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($booking['booking_number']) ?></td>
                        <td><?= e($booking['passenger_name']) ?></td>
                        <td><?= e($booking['origin']) ?> to <?= e($booking['destination']) ?></td>
                        <td><span class="badge text-bg-primary"><?= e($booking['seat_number']) ?></span></td>
                        <td><span class="badge <?= e(status_badge_class($booking['booking_status'])) ?>"><?= e($booking['booking_status']) ?></span></td>
                        <td><span class="badge <?= e(status_badge_class($booking['payment_status'])) ?>"><?= e($booking['payment_status']) ?></span></td>
                        <td>
                            <?php if (!empty($booking['ticket_number'])): ?>
                                <span class="badge <?= e(status_badge_class($booking['ticket_status'] ?? 'active')) ?>"><?= e($booking['ticket_number']) ?></span>
                            <?php else: ?>
                                <span class="badge text-bg-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <?php if (($booking['booking_status'] ?? '') === 'pending' || empty($booking['ticket_number'])): ?>
                                    <form method="post" action="<?= url('admin/bookings/' . $booking['id'] . '/approve') ?>" class="d-inline">
                                        <?= \Transport\Core\Csrf::field() ?>
                                        <button class="btn btn-sm btn-primary">Approve</button>
                                    </form>
                                    <form method="post" action="<?= url('admin/bookings/' . $booking['id'] . '/reject') ?>" class="d-inline" data-confirm="Reject this booking?">
                                        <?= \Transport\Core\Csrf::field() ?>
                                        <button class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge text-bg-success">Approved</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$bookings): ?>
        <div class="empty-state">
            <i class="bi bi-receipt"></i>
            <h4>No bookings found</h4>
            <p class="text-muted mb-0">Try a different search or wait for new bookings.</p>
        </div>
    <?php endif; ?>
</section>
