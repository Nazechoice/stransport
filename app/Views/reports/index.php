<section class="page-heading mb-4">
    <div>
        <div class="mini-label">Analytics</div>
        <h1 class="page-title mt-1">Report center</h1>
        <p>Revenue, utilization, and route performance.</p>
    </div>
</section>

<section class="panel-card mb-4">
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a class="btn btn-outline-primary" href="<?= url('reports/export/daily/csv') ?>">Daily CSV</a>
        <a class="btn btn-outline-primary" href="<?= url('reports/export/weekly/csv') ?>">Weekly CSV</a>
        <a class="btn btn-outline-primary" href="<?= url('reports/export/monthly/csv') ?>">Monthly CSV</a>
        <a class="btn btn-outline-primary" href="<?= url('reports/export/yearly/csv') ?>">Yearly CSV</a>
        <a class="btn btn-outline-primary" href="<?= url('reports/export/monthly/excel') ?>">Monthly Excel</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="info-card h-100">
                <div class="mini-label mb-2">Bookings</div>
                <canvas id="reportChart" height="160"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="info-card h-100">
                <div class="mini-label mb-2">Revenue mix</div>
                <canvas id="utilChart" height="160"></canvas>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <h4 class="mb-3">Top Routes</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Route</th><th>Bookings</th></tr></thead>
                <tbody>
                    <?php foreach ($summary['top_routes'] as $route): ?>
                        <tr><td><?= e($route['origin']) ?> to <?= e($route['destination']) ?></td><td><?= e($route['bookings']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-card">
        <h4 class="mb-3">Bus Utilization</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Bus</th><th>Trips</th></tr></thead>
                <tbody>
                    <?php foreach ($summary['bus_usage'] as $bus): ?>
                        <tr><td><?= e($bus['bus_number']) ?></td><td><?= e($bus['trips']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('reportChart'), {
        type: 'bar',
        data: {
            labels: ['Bookings', 'Passengers', 'Routes', 'Tickets'],
            datasets: [{
                label: 'Count',
                data: [<?= (int)$stats['bookings'] ?>, <?= (int)($stats['passengers'] ?? $stats['users']) ?>, <?= (int)$stats['routes'] ?>, <?= (int)$stats['tickets'] ?>],
                backgroundColor: '#2563eb'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('utilChart'), {
        type: 'doughnut',
        data: {
            labels: ['Revenue', 'Pending'],
            datasets: [{
                data: [<?= (float)$stats['revenue'] ?>, <?= (int)$stats['pending_payments'] ?>],
                backgroundColor: ['#2563eb', '#cbd5e1']
            }]
        },
        options: { responsive: true }
    });
</script>
