<?php $pageTitle = 'Verify Ticket'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Verification</div>
        <h1 class="dashboard-hero__title">Premium ticket verification for faster boarding.</h1>
        <p class="dashboard-hero__copy">
            Search by ticket number or open the QR scanner. Results are displayed instantly in a clean officer-friendly layout.
        </p>
        <div class="dashboard-hero__actions">
            <a href="<?= url('officer/scanner') ?>" class="btn btn-light btn-lg">
                <i class="bi bi-camera-video me-2"></i>
                Open scanner
            </a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Boarding desk</div>
            <div class="mini-value">Ticket checks</div>
            <div class="mini-note">Verify eligibility before passengers move to the bus.</div>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-3">
            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-shield-check me-1"></i> CSRF protected</span>
        </div>
    </aside>
</section>

<section class="panel-card mb-4">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Ticket lookup</div>
            <h2 class="page-title mt-1">Verify ticket</h2>
            <p>Enter a ticket number to confirm passenger details.</p>
        </div>
    </div>

    <form method="post" action="<?= url('officer/verify') ?>" class="row g-3" id="verifyForm">
        <?= \Transport\Core\Csrf::field() ?>
        <div class="col-md-9">
            <label class="form-label">Ticket number</label>
            <input
                name="ticket_number"
                class="form-control form-control-lg"
                value="<?= e($ticketNumber ?? '') ?>"
                placeholder="e.g. PBT-TKT-XXXX"
                inputmode="text"
                autocomplete="off"
            >
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary btn-lg w-100" type="submit">
                <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-search"></i>
                    Verify
                </span>
            </button>
        </div>
    </form>

    <div class="mt-3 d-flex gap-2 flex-wrap">
        <a href="<?= url('officer/scanner') ?>" class="btn btn-outline-primary">
            <i class="bi bi-qr-code-scan me-2"></i>
            Open QR scanner
        </a>
    </div>
</section>

<?php if (!empty($ticket)): ?>
    <section class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Verification result</div>
                <h2 class="page-title mt-1">Passenger details</h2>
                <p>Details returned for officer confirmation.</p>
            </div>
            <span class="badge <?= ($ticket['status'] ?? '') === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : (($ticket['status'] ?? '') === 'used' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle') ?>">
                <i class="bi bi-dot me-1"></i>
                <?= e($ticket['status']) ?>
            </span>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-label">Passenger</div>
                    <div class="stat-value fs-4"><?= e($ticket['passenger_name']) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-label">Booking</div>
                    <div class="stat-value fs-4"><?= e($ticket['booking_number']) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-label">Seat</div>
                    <div class="stat-value fs-4"><?= e($ticket['seat_number']) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-label">Ticket number</div>
                    <div class="stat-value fs-4"><?= e($ticket['ticket_number']) ?></div>
                </div>
            </div>
        </div>

        <?php if (($ticket['status'] ?? '') === 'active'): ?>
            <div class="mt-4 d-flex flex-wrap gap-2">
                <form method="post" action="<?= url('officer/tickets/' . $ticket['id'] . '/use') ?>">
                    <?= \Transport\Core\Csrf::field() ?>
                    <button class="btn btn-primary">
                        <i class="bi bi-check2-circle me-2"></i>
                        Mark as used
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </section>
<?php elseif (!empty($ticketNumber)): ?>
    <section class="panel-card">
        <div class="empty-state mb-0">
            <i class="bi bi-exclamation-triangle"></i>
            <h4>No ticket found</h4>
            <p class="text-muted mb-0">No ticket was found for that number.</p>
        </div>
    </section>
<?php endif; ?>
