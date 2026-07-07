<?php
$pageTitle = 'Available Vehicles';
?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Fleet gallery</div>
        <h1 class="dashboard-hero__title">Browse the available buses before you book.</h1>
        <p class="dashboard-hero__copy">
            View vehicle photos, capacities, types, and assigned drivers in a clean card layout.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('journey/search') ?>">Book a trip</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('routes') ?>">View routes</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Fleet</div>
            <div class="mini-value"><?= e($stats['buses']) ?> vehicles</div>
            <div class="mini-note">Active and maintenance vehicles are shown below.</div>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--three">
    <?php foreach ($vehicles as $vehicle): ?>
        <article class="journey-card">
            <?php if (!empty($vehicle['image'])): ?>
                <img src="<?= url($vehicle['image']) ?>" alt="<?= e($vehicle['bus_number']) ?>" class="rounded-4 mb-3 w-100" style="height: 180px; object-fit: cover;">
            <?php else: ?>
                <div class="empty-state mb-3" style="min-height: 180px;">
                    <i class="bi bi-bus-front"></i>
                    <h4>No image available</h4>
                </div>
            <?php endif; ?>

            <div class="journey-pill mb-3"><i class="bi bi-bus-front"></i> <?= e($vehicle['status']) ?></div>
            <h3 class="route-title mb-2"><?= e($vehicle['bus_number']) ?></h3>
            <div class="route-meta mb-2"><?= e($vehicle['bus_type']) ?> | Capacity <?= e($vehicle['capacity']) ?></div>
            <div class="small text-muted mb-3"><?= e($vehicle['registration_number']) ?></div>
            <div class="small mb-2"><strong>Driver:</strong> <?= e($vehicle['driver_name'] ?? 'Unassigned') ?></div>
            <div class="small mb-3"><strong>Contact:</strong> <?= e($vehicle['driver_phone'] ?? 'N/A') ?></div>
            <a class="btn btn-outline-primary btn-sm" href="<?= url('journey/search?bus_id=' . (int) $vehicle['id']) ?>">Book with this fleet</a>
        </article>
    <?php endforeach; ?>
</section>

<?php if (!$vehicles): ?>
    <section class="panel-card">
        <div class="empty-state">
            <i class="bi bi-bus-front"></i>
            <h4>No vehicles available</h4>
            <p class="text-muted mb-0">Add buses in the admin panel to show them here.</p>
        </div>
    </section>
<?php endif; ?>
