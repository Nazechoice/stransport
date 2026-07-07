<section class="page-heading mb-4">
    <div>
        <div class="mini-label">Seat selection</div>
        <h1 class="page-title mt-1">Choose your seat</h1>
        <p><?= e($schedule['origin']) ?> to <?= e($schedule['destination']) ?> | <?= e($schedule['departure_date']) ?> at <?= e($schedule['departure_time']) ?> | <?= e($schedule['bus_number'] ?? '') ?> | <?= money($schedule['fare']) ?></p>
    </div>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Seat map</div>
                <h2 class="page-title mt-1">Pick an available seat</h2>
                <p>Green seats are available, red are booked, and gray seats are blocked.</p>
            </div>
            <span class="badge text-bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-info-circle me-1"></i> Tap a green seat</span>
        </div>

        <div id="seatGrid" class="seat-grid" data-schedule-id="<?= (int) $schedule['id'] ?>"></div>
    </div>

    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Booking details</div>
                <h2 class="page-title mt-1">Confirm your journey</h2>
                <p>Step 2 of 4 in the booking flow.</p>
            </div>
            <span class="badge text-bg-success-subtle text-success border border-success-subtle">2 / 4</span>
        </div>

        <div class="progress mb-4" style="height: 10px;">
            <div class="progress-bar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <form method="post" action="<?= url('booking/confirm') ?>" id="bookingForm" class="needs-validation" novalidate>
            <?= \Transport\Core\Csrf::field() ?>
            <input type="hidden" name="schedule_id" value="<?= (int) $schedule['id'] ?>">
            <input type="hidden" name="seat_number" id="seat_number" value="">
            <input type="hidden" name="passenger_id" value="<?= (int) (auth_user()['id'] ?? 0) ?>">
            <input type="hidden" name="booking_type" value="online">

            <div class="mb-3">
                <label class="form-label">Selected seat</label>
                <input class="form-control form-control-lg" id="selectedSeatDisplay" readonly placeholder="Choose a seat" aria-label="Selected seat">
            </div>

            <div class="mb-3">
                <label class="form-label">Payment method</label>
                <select class="form-select form-select-lg" name="payment_method" required>
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                    <option value="card">Card</option>
                </select>
                <div class="invalid-feedback">Please select a payment method.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Payment status</label>
                <select class="form-select form-select-lg" name="payment_status" required>
                    <option value="paid" selected>Paid now</option>
                    <option value="pending">Needs approval</option>
                </select>
                <div class="form-text">Choose approval if you want the ticket issued after admin review.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="4" placeholder="Optional notes"></textarea>
            </div>

            <button class="btn btn-primary btn-lg w-100" id="confirmBookingBtn" disabled>
                <span class="d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-circle"></i>
                    Confirm Booking
                </span>
            </button>
        </form>
    </div>
</section>
