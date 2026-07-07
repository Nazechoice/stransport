<?php $pageTitle = 'Admin Dashboard'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Command center</div>
        <h1 class="dashboard-hero__title">Run transport operations from one premium workspace.</h1>
        <p class="dashboard-hero__copy">
            Track revenue, bookings, fleet movement, and payment health with a dashboard designed for daily admin control.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('admin/reports') ?>">Open analytics</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('admin/schedules') ?>">Create schedule</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('admin/users') ?>">Manage users</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Live snapshot</div>
            <div class="mini-value"><?= money($stats['revenue']) ?></div>
            <div class="mini-note">Revenue recorded across active operations.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Bookings <?= e($stats['bookings']) ?></span>
            <span class="badge text-bg-info">Tickets <?= e($stats['tickets']) ?></span>
            <span class="badge text-bg-warning">Pending <?= e($stats['pending_payments']) ?></span>
        </div>
        <div class="timeline">
            <div class="timeline-item">
                <div>
                    <h6>Fleet health</h6>
                    <p><?= e($stats['buses']) ?> buses and <?= e($stats['routes']) ?> routes are tracked here.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div>
                    <h6>Network scale</h6>
                    <p><?= e($stats['schedules']) ?> schedules and <?= e($stats['passengers']) ?> passengers are in the system.</p>
                </div>
            </div>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--four">
    <div class="stat-card">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <div class="feature-icon"><i class="bi bi-currency-exchange"></i></div>
                <div class="stat-value"><?= money($stats['revenue']) ?></div>
                <div class="stat-label">Total revenue</div>
            </div>
            <span class="badge text-bg-success">Finance</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <div class="feature-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-value"><?= e($stats['bookings']) ?></div>
                <div class="stat-label">Bookings created</div>
            </div>
            <span class="badge text-bg-primary">Demand</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <div class="feature-icon"><i class="bi bi-ticket-perforated"></i></div>
                <div class="stat-value"><?= e($stats['tickets']) ?></div>
                <div class="stat-label">Issued tickets</div>
            </div>
            <span class="badge text-bg-info">Boarding</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-value"><?= e($stats['pending_payments']) ?></div>
                <div class="stat-label">Pending payments</div>
            </div>
            <span class="badge text-bg-warning">Finance</span>
        </div>
    </div>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card chart-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Booking trend</div>
                <h2 class="page-title mt-1">Weekly booking activity</h2>
                <p>Snapshot of booking flow across the current dashboard window.</p>
            </div>
        </div>
        <canvas id="bookingsChart" height="120"></canvas>
    </div>

    <div class="panel-card chart-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Route demand</div>
                <h2 class="page-title mt-1">Popular routes</h2>
                <p>Demand distribution across your most active corridors.</p>
            </div>
        </div>
        <canvas id="revenueChart" height="120"></canvas>
    </div>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Recent bookings</div>
                <h2 class="page-title mt-1">Latest reservations</h2>
                <p>Bookings most recently created in the system.</p>
            </div>
            <a class="btn btn-outline-primary btn-sm" href="<?= url('admin/bookings') ?>">View all</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Passenger</th>
                        <th>Route</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($booking['booking_number']) ?></td>
                            <td><?= e($booking['passenger_name']) ?></td>
                            <td><?= e($booking['origin']) ?> to <?= e($booking['destination']) ?></td>
                            <td><span class="badge <?= e(status_badge_class($booking['booking_status'])) ?>"><?= e($booking['booking_status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Recent payments</div>
                <h2 class="page-title mt-1">Finance feed</h2>
                <p>Payment activity and settlement status.</p>
            </div>
            <a class="btn btn-outline-primary btn-sm" href="<?= url('admin/payments') ?>">View all</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Passenger</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPayments as $payment): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($payment['payment_reference']) ?></td>
                            <td><?= e($payment['passenger_name']) ?></td>
                            <td><?= money($payment['amount']) ?></td>
                            <td><span class="badge <?= e(status_badge_class($payment['status'])) ?>"><?= e($payment['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Popular routes</div>
            <h2 class="page-title mt-1">Highest demand corridors</h2>
            <p>Use this route intelligence to prioritize schedules and fleet planning.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Route</th>
                    <th>Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($popularRoutes as $route): ?>
                    <tr>
                        <td><?= e($route['origin']) ?> to <?= e($route['destination']) ?></td>
                        <td><span class="badge text-bg-primary"><?= e($route['bookings']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    (function () {
        function initCharts() {
            if (!window.Chart) {
                return;
            }

            const bookingsCtx = document.getElementById('bookingsChart');
            if (bookingsCtx) {
                new Chart(bookingsCtx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Bookings',
                            data: [12, 19, 14, 24, 18, 29, 22],
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37,99,235,0.12)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: 'rgba(148,163,184,0.16)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode(array_map(static fn($r) => $r['origin'] . ' to ' . $r['destination'], $popularRoutes)) ?>,
                        datasets: [{
                            data: <?= json_encode(array_map(static fn($r) => (int) $r['bookings'], $popularRoutes)) ?>,
                            backgroundColor: ['#2563eb', '#0f172a', '#0ea5e9', '#93c5fd', '#60a5fa', '#1d4ed8']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        }

        function loadCharts() {
            if (window.Chart) {
                initCharts();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.async = true;
            script.onload = initCharts;
            document.head.appendChild(script);
        }

        if (document.readyState === 'complete') {
            loadCharts();
        } else {
            window.addEventListener('load', loadCharts, { once: true });
        }
    })();
</script>
