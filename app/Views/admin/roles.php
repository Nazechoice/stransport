<?php $pageTitle = 'Role Management'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Authorization model</div>
        <h1 class="dashboard-hero__title">Understand every role in the transport system.</h1>
        <p class="dashboard-hero__copy">
            The application uses role-based access control to separate administrative, operational, and passenger workflows.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('admin/users') ?>">Manage users</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('admin/logs') ?>">View logs</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Role coverage</div>
            <div class="mini-value"><?= e(count($roles)) ?> roles</div>
            <div class="mini-note">Each role has a defined dashboard and permission scope.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Admin</span>
            <span class="badge text-bg-info">Officer</span>
            <span class="badge text-bg-success">Driver</span>
            <span class="badge text-bg-secondary">Passenger</span>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--three">
    <?php foreach ($roles as $roleKey => $roleName): ?>
        <?php
        $descriptions = [
            'super_admin' => 'Full system control, including backups, restore, user management, and configuration.',
            'administrator' => 'Fleet, route, schedule, booking, payment, and reporting operations.',
            'ticket_officer' => 'Walk-in booking, QR verification, ticket printing, and boarding desk operations.',
            'driver' => 'Assigned trip, manifest review, and trip completion workflow.',
            'passenger' => 'Search journeys, reserve seats, manage tickets, and review travel history.',
        ];
        ?>
        <article class="feature-card">
            <i class="bi <?= e(match ($roleKey) {
                'super_admin' => 'bi-shield-lock',
                'administrator' => 'bi-gear-wide-connected',
                'ticket_officer' => 'bi-qr-code-scan',
                'driver' => 'bi-truck',
                default => 'bi-person',
            }) ?> feature-icon"></i>
            <h5><?= e($roleName) ?></h5>
            <div class="mini-note mb-2"><?= e($roleCounts[$roleKey] ?? 0) ?> accounts assigned</div>
            <p class="feature-copy mb-0"><?= e($descriptions[$roleKey] ?? 'System role.') ?></p>
        </article>
    <?php endforeach; ?>
</section>

<section class="landing-section">
    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Permissions snapshot</div>
                <h2 class="page-title mt-1">Role responsibilities at a glance</h2>
                <p>The page is intentionally read-only so the authorization model stays predictable and safe.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Accounts</th>
                        <th>Primary responsibility</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $roleKey => $roleName): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($roleName) ?></td>
                            <td><?= e($roleCounts[$roleKey] ?? 0) ?></td>
                            <td><?= e(match ($roleKey) {
                                'super_admin' => 'System administration and oversight',
                                'administrator' => 'Operational management',
                                'ticket_officer' => 'Verification and boarding',
                                'driver' => 'Trip execution and manifest control',
                                default => 'Journey booking and ticket access',
                            }) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
