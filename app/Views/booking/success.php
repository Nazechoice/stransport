<?php $pageTitle = 'Booking Success'; ?>
<?php if (!empty($summary['ticket_id'])): ?>
    <meta http-equiv="refresh" content="2;url=<?= e(url('tickets/' . $summary['ticket_id'])) ?>">
<?php endif; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Booking confirmed</div>
        <h1 class="dashboard-hero__title">Your trip is ready.</h1>
        <p class="dashboard-hero__copy">
            Review the booking summary, open your ticket, and continue to your booking history whenever you need it.
        </p>
        <div class="dashboard-hero__actions">
            <?php if (!empty($summary['ticket_id'])): ?>
                <a class="btn btn-light btn-lg" href="<?= url('tickets/' . $summary['ticket_id']) ?>">View Ticket</a>
                <a class="btn btn-outline-light btn-lg" href="<?= url('tickets/' . $summary['ticket_id'] . '/download') ?>">Download PDF</a>
            <?php endif; ?>
            <a class="btn btn-outline-light btn-lg" href="<?= url('bookings/history') ?>">Booking History</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Booking no</div>
            <div class="mini-value"><?= e($summary['booking_number']) ?></div>
            <div class="mini-note">Payment: <?= e($summary['payment_status']) ?> | Seat <?= e($summary['seat_number']) ?></div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary"><?= money($summary['total_amount']) ?></span>
            <span class="badge text-bg-info"><?= e($summary['payment_method']) ?></span>
            <?php if (!empty($summary['ticket_number'])): ?>
                <span class="badge text-bg-success"><?= e($summary['ticket_number']) ?></span>
            <?php endif; ?>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Summary</div>
                <h2 class="page-title mt-1">Your booking details</h2>
                <p>All key booking fields are shown here for confirmation and reference.</p>
            </div>
        </div>

        <div class="ticket-meta-grid">
            <div><span>Passenger</span><strong><?= e($summary['passenger_name']) ?></strong></div>
            <div><span>Bus</span><strong><?= e($summary['bus_number']) ?></strong></div>
            <div><span>Route</span><strong><?= e($summary['origin']) ?> to <?= e($summary['destination']) ?></strong></div>
            <div><span>Departure</span><strong><?= e($summary['departure_date']) ?> <?= e($summary['departure_time']) ?></strong></div>
            <div><span>Arrival</span><strong><?= e($summary['arrival_time']) ?></strong></div>
            <div><span>Seat</span><strong><?= e($summary['seat_number']) ?></strong></div>
            <div><span>Fare</span><strong><?= money($summary['route_fare']) ?></strong></div>
            <div><span>Total amount</span><strong><?= money($summary['total_amount']) ?></strong></div>
        </div>
    </div>

    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Next actions</div>
                <h2 class="page-title mt-1">Continue your journey</h2>
                <p>Use the ticket or return to your travel history from here.</p>
            </div>
        </div>

        <?php if (!empty($summary['ticket_id'])): ?>
            <div class="empty-state mb-4">
                <i class="bi bi-ticket-perforated"></i>
                <h4>Ticket generated successfully</h4>
                <p class="text-muted mb-0">Your QR ticket is ready for download, printing, and boarding validation.</p>
            </div>
            <div class="ticket-actions">
                <a class="btn btn-primary" href="<?= url('tickets/' . $summary['ticket_id']) ?>">View Ticket</a>
                <a class="btn btn-outline-primary" href="<?= url('tickets/' . $summary['ticket_id'] . '/print') ?>" target="_blank" rel="noopener">Print Ticket</a>
                <a class="btn btn-outline-primary" href="<?= url('tickets/' . $summary['ticket_id'] . '/download') ?>">Download Ticket</a>
                <a class="btn btn-outline-secondary" href="<?= url('passenger/tickets') ?>">My Tickets</a>
            </div>
        <?php else: ?>
            <div class="empty-state mb-4">
                <i class="bi bi-clock-history"></i>
                <h4>Booking submitted for approval</h4>
                <p class="text-muted mb-0">Payment is marked as pending, so the ticket will be issued once the payment is confirmed.</p>
            </div>
            <div class="ticket-actions">
                <a class="btn btn-primary" href="<?= url('bookings/history') ?>">View Booking History</a>
                <a class="btn btn-outline-primary" href="<?= url('journey/search') ?>">Search Another Trip</a>
            </div>
        <?php endif; ?>
    </div>
</section>
