<?php $pageTitle = 'Travel History'; ?>

<section class="page-heading mb-4">
    <div>
        <div class="mini-label">Travel history</div>
        <h1 class="page-title mt-1">Completed journeys</h1>
        <p>Completed journeys and boarding records.</p>
    </div>
</section>

<section class="panel-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Route</th>
                    <th>Seat</th>
                    <th>Departure</th>
                    <th>Ticket</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $row): ?>
                    <tr>
                        <td><?= e($row['booking_number']) ?></td>
                        <td><?= e($row['origin']) ?> to <?= e($row['destination']) ?></td>
                        <td><?= e($row['seat_number']) ?></td>
                        <td><?= e($row['departure_date']) ?> <?= e($row['departure_time']) ?></td>
                        <td><?= e($row['ticket_number'] ?? 'Pending') ?></td>
                        <td><?= e($row['booking_status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$history): ?>
        <div class="empty-state mt-3">
            <i class="bi bi-clock-history"></i>
            <h4>No travel history yet</h4>
            <p class="text-muted">Your completed trips will appear here after you travel.</p>
            <a href="<?= url('journey/search') ?>" class="btn btn-primary">Search Trips</a>
        </div>
    <?php endif; ?>
</section>
