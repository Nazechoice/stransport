<section class="ticket-card">
    <div class="ticket-header">
        <div>
            <div class="mini-label">Ticket number</div>
            <h1 class="page-title mt-1 mb-0"><?= e($ticket['ticket_number']) ?></h1>
        </div>
        <div class="text-end">
            <div class="mini-label">Booking</div>
            <h2 class="h4 mb-0"><?= e($ticket['booking_number']) ?></h2>
        </div>
    </div>

    <div class="row g-4 align-items-center mt-2">
        <div class="col-md-8">
            <div class="ticket-meta-grid">
                <div><span>Passenger</span><strong><?= e($ticket['passenger_name']) ?></strong></div>
                <div><span>Route</span><strong><?= e($ticket['origin']) ?> to <?= e($ticket['destination']) ?></strong></div>
                <div><span>Bus</span><strong><?= e($ticket['bus_number']) ?></strong></div>
                <div><span>Seat</span><strong><?= e($ticket['seat_number']) ?></strong></div>
                <div><span>Departure</span><strong><?= e($ticket['departure_date']) ?> <?= e($ticket['departure_time']) ?></strong></div>
                <div><span>Status</span><strong><?= e($ticket['status']) ?></strong></div>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="qr-wrap"><?= $svg ?></div>
        </div>
    </div>

    <div class="ticket-actions mt-4">
        <a class="btn btn-primary" href="<?= url('tickets/' . $ticket['id'] . '/download') ?>">Download PDF</a>
        <a class="btn btn-outline-primary" href="<?= url('tickets/' . $ticket['id'] . '/print') ?>" target="_blank" rel="noopener">Print Ticket</a>
        <a class="btn btn-outline-secondary" href="<?= url('dashboard') ?>">Back to Dashboard</a>
    </div>
</section>
