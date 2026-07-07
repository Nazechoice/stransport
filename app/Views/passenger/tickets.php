<?php $pageTitle = 'My Tickets'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Boarding passes</div>
        <h1 class="dashboard-hero__title">All issued tickets in one polished list.</h1>
        <p class="dashboard-hero__copy">
            Open your ticket, download it, and present it at boarding with less friction.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('journey/search') ?>">Book a ticket</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('bookings/history') ?>">View history</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Ticket count</div>
            <div class="mini-value"><?= e(count($tickets)) ?> issued</div>
            <div class="mini-note">Keep a copy handy for scanning and boarding verification.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Ticket directory</div>
            <h2 class="page-title mt-1">My tickets</h2>
            <p>Access your issued tickets and boarding passes.</p>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="<?= url('journey/search') ?>">Book more</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Route</th>
                    <th>Seat</th>
                    <th>Departure</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($ticket['ticket_number']) ?></td>
                        <td><?= e($ticket['origin']) ?> to <?= e($ticket['destination']) ?></td>
                        <td><span class="badge text-bg-primary"><?= e($ticket['seat_number']) ?></span></td>
                        <td><?= e($ticket['departure_date']) ?> <?= e($ticket['departure_time']) ?></td>
                        <td class="text-end">
                            <a href="<?= url('tickets/' . $ticket['id']) ?>" class="btn btn-sm btn-primary">View</a>
                            <a href="<?= url('tickets/' . $ticket['id'] . '/download') ?>" class="btn btn-sm btn-outline-primary">Download</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$tickets): ?>
        <div class="empty-state mt-3">
            <i class="bi bi-ticket-perforated"></i>
            <h4>No tickets yet</h4>
            <p class="text-muted mb-3">Your issued tickets will show here.</p>
            <a href="<?= url('journey/search') ?>" class="btn btn-primary">Book a ticket</a>
        </div>
    <?php endif; ?>
</section>
