<?php $pageTitle = 'Walk-in Booking'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Counter booking</div>
        <h1 class="dashboard-hero__title">Create walk-in bookings with a premium desk workflow.</h1>
        <p class="dashboard-hero__copy">
            Use this form to issue a booking and ticket directly at the counter without changing the core booking logic.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Walk-in desk</div>
            <div class="mini-value">On-site booking</div>
            <div class="mini-note">Quickly select a passenger, schedule, and seat.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-4">
        <div>
            <div class="mini-label">Walk-in booking</div>
            <h2 class="page-title mt-1">Issue ticket</h2>
            <p>Create a booking and issue a ticket at the counter.</p>
        </div>
    </div>

    <form method="post" action="<?= url('booking/confirm') ?>" class="row g-3">
        <?= \Transport\Core\Csrf::field() ?>
        <input type="hidden" name="booking_type" value="walk_in">
        <div class="col-md-4">
            <label class="form-label">Passenger</label>
            <select name="passenger_id" class="form-select form-select-lg" required>
                <option value="">Select passenger</option>
                <?php foreach ($passengers as $passenger): ?>
                    <option value="<?= (int) $passenger['id'] ?>"><?= e($passenger['full_name']) ?> (<?= e($passenger['phone']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Schedule</label>
            <select name="schedule_id" class="form-select form-select-lg" required>
                <option value="">Select schedule</option>
                <?php foreach ($schedules as $schedule): ?>
                    <option value="<?= (int) $schedule['id'] ?>"><?= e($schedule['origin']) ?> to <?= e($schedule['destination']) ?> - <?= e($schedule['departure_date']) ?> <?= e($schedule['departure_time']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Seat number</label>
            <input name="seat_number" class="form-control form-control-lg" placeholder="e.g. A1" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Payment method</label>
            <select name="payment_method" class="form-select form-select-lg" required>
                <option value="cash">Cash</option>
                <option value="transfer">Transfer</option>
                <option value="card">Card</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Payment status</label>
            <select name="payment_status" class="form-select form-select-lg" required>
                <option value="paid" selected>Paid now</option>
                <option value="pending">Needs approval</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="4" placeholder="Optional notes"></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary btn-lg">Create Walk-in Booking</button>
        </div>
    </form>
</section>
