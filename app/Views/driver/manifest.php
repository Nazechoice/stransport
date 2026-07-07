<?php $pageTitle = 'Passenger Manifest'; ?>

<section class="page-heading mb-4">
    <div>
        <div class="mini-label">Manifest</div>
        <h1 class="page-title mt-1">Passenger manifest</h1>
        <p><?= e($schedule['origin']) ?> to <?= e($schedule['destination']) ?></p>
    </div>
</section>

<section class="panel-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Booking</th><th>Passenger</th><th>Phone</th><th>Seat</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($manifest as $row): ?>
                    <tr>
                        <td><?= e($row['booking_number']) ?></td>
                        <td><?= e($row['full_name']) ?></td>
                        <td><?= e($row['phone']) ?></td>
                        <td><?= e($row['seat_number']) ?></td>
                        <td><span class="badge <?= e(status_badge_class($row['booking_status'])) ?>"><?= e($row['booking_status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
