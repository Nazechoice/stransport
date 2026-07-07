<?php $pageTitle = 'Payments'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Revenue operations</div>
        <h1 class="dashboard-hero__title">Payment records with a cleaner finance view.</h1>
        <p class="dashboard-hero__copy">
            Monitor receipts, statuses, and passenger payments with stronger readability.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Payments</div>
            <div class="mini-value"><?= e(count($payments)) ?> records</div>
            <div class="mini-note">Search by reference, booking, or passenger name.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-success">Paid</span>
            <span class="badge text-bg-warning">Pending</span>
            <span class="badge text-bg-danger">Failed</span>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Payment records</div>
            <h2 class="page-title mt-1">Finance ledger</h2>
            <p>A searchable list of receipts and transaction statuses.</p>
        </div>
    </div>

    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#paymentsTable" placeholder="Search payments by reference, passenger, or booking">
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="paymentsTable">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Passenger</th>
                    <th>Booking</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($payment['payment_reference']) ?></td>
                        <td><?= e($payment['passenger_name']) ?></td>
                        <td><?= e($payment['booking_number']) ?></td>
                        <td><?= money($payment['amount']) ?></td>
                        <td><?= e($payment['method']) ?></td>
                        <td><span class="badge <?= e(status_badge_class($payment['status'])) ?>"><?= e($payment['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$payments): ?>
        <div class="empty-state">
            <i class="bi bi-cash-stack"></i>
            <h4>No payments recorded</h4>
            <p class="text-muted mb-0">Payment records will appear here after bookings are paid.</p>
        </div>
    <?php endif; ?>
</section>
