<?php $pageTitle = 'Boarding List'; ?>

<section class="page-heading mb-4">
    <div>
        <div class="mini-label">Boarding list</div>
        <h1 class="page-title mt-1"><?= e($schedule['origin']) ?> to <?= e($schedule['destination']) ?></h1>
        <p>Passengers cleared for this departure.</p>
    </div>
</section>

<section class="panel-card">
    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#boardingTable" placeholder="Search passengers or booking numbers">
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="boardingTable">
            <thead>
                <tr>
                    <th>Passenger</th>
                    <th>Booking</th>
                    <th>Phone</th>
                    <th>Seat</th>
                    <th>Ticket</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($manifest as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($row['full_name']) ?></td>
                        <td><?= e($row['booking_number']) ?></td>
                        <td><?= e($row['phone']) ?></td>
                        <td><?= e($row['seat_number']) ?></td>
                        <td><?= e($row['ticket_number'] ?? 'Pending') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$manifest): ?>
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h4>No passengers on this trip</h4>
            <p class="text-muted">The boarding list will populate when bookings are made.</p>
        </div>
    <?php endif; ?>
</section>
