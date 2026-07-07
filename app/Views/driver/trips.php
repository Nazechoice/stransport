<?php $pageTitle = 'Assigned Trips'; ?>

<section class="page-heading mb-4">
    <div>
        <div class="mini-label">Driver workspace</div>
        <h1 class="page-title mt-1">Assigned trips</h1>
        <p>Trips currently assigned to you.</p>
    </div>
</section>

<section class="panel-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Route</th><th>Departure</th><th>Bus</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($trip['origin']) ?> to <?= e($trip['destination']) ?></td>
                        <td><?= e($trip['departure_date']) ?> <?= e($trip['departure_time']) ?></td>
                        <td><?= e($trip['bus_number']) ?></td>
                        <td><span class="badge <?= e(status_badge_class($trip['status'])) ?>"><?= e($trip['status']) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= url('driver/manifest/' . $trip['id']) ?>">View Manifest</a>
                            <form action="<?= url('driver/complete-trip/' . $trip['id']) ?>" method="post" class="d-inline" data-confirm="Mark this trip as completed?">
                                <?= \Transport\Core\Csrf::field() ?>
                                <button class="btn btn-sm btn-primary">Complete Trip</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$trips): ?>
        <div class="empty-state">
            <i class="bi bi-truck"></i>
            <h4>No trips assigned</h4>
            <p class="text-muted">Assigned trips will appear here once the administrator schedules them.</p>
        </div>
    <?php endif; ?>
</section>
