<?php $pageTitle = 'Manage Schedules'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Trip planning</div>
        <h1 class="dashboard-hero__title">Plan departures with a cleaner scheduling view.</h1>
        <p class="dashboard-hero__copy">
            Assign buses, drivers, routes, and fares in one structured workflow built for daily operations.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="#scheduleForm">Add schedule</a>
            <button class="btn btn-outline-light btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#scheduleModal">Open editor</button>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Schedule board</div>
            <div class="mini-value"><?= e(count($schedules)) ?> trips</div>
            <div class="mini-note">Search and update departures without leaving the page.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Scheduled</span>
            <span class="badge text-bg-info">Boarding</span>
            <span class="badge text-bg-success">Completed</span>
            <span class="badge text-bg-danger">Cancelled</span>
        </div>
    </aside>
</section>

<section class="panel-card mb-4" id="scheduleSection">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Create schedule</div>
            <h2 class="page-title mt-1">Departure plan</h2>
            <p>Combine route, bus, and driver availability in one form.</p>
        </div>
    </div>

    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#schedulesTable" placeholder="Search schedules by route, bus, or driver">
    </div>

    <form method="post" action="<?= url('admin/schedules/save') ?>" id="scheduleForm" class="row g-3">
        <?= \Transport\Core\Csrf::field() ?>
        <input type="hidden" name="id" value="">
        <div class="col-md-2">
            <label class="form-label">Bus</label>
            <select name="bus_id" class="form-select" required>
                <option value="">Bus</option>
                <?php foreach ($buses as $bus): ?><option value="<?= (int) $bus['id'] ?>"><?= e($bus['bus_number']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Driver</label>
            <select name="driver_id" class="form-select" required>
                <option value="">Driver</option>
                <?php foreach ($drivers as $driver): ?><option value="<?= (int) $driver['id'] ?>"><?= e($driver['full_name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Route</label>
            <select name="route_id" class="form-select" required>
                <option value="">Route</option>
                <?php foreach ($routes as $route): ?><option value="<?= (int) $route['id'] ?>"><?= e($route['origin']) ?> to <?= e($route['destination']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Departure date</label>
            <input type="date" name="departure_date" class="form-control">
        </div>
        <div class="col-md-1">
            <label class="form-label">Departure</label>
            <input type="time" name="departure_time" class="form-control">
        </div>
        <div class="col-md-1">
            <label class="form-label">Arrival</label>
            <input type="time" name="arrival_time" class="form-control">
        </div>
        <div class="col-md-1">
            <label class="form-label">Seats</label>
            <input type="number" name="available_seats" class="form-control" placeholder="Seats">
        </div>
        <div class="col-md-1">
            <label class="form-label">Fare</label>
            <input type="number" step="0.01" name="fare" class="form-control" placeholder="Fare">
        </div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="scheduled">Scheduled</option>
                <option value="boarding">Boarding</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-12">
            <button class="btn btn-primary btn-lg">Save schedule</button>
        </div>
    </form>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Schedule board</div>
            <h2 class="page-title mt-1">Trips</h2>
            <p>Track planned trips and update their status live.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="schedulesTable">
            <thead>
                <tr>
                    <th>Route</th>
                    <th>Bus</th>
                    <th>Driver</th>
                    <th>Departure</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($schedule['origin']) ?> to <?= e($schedule['destination']) ?></td>
                        <td><?= e($schedule['bus_number']) ?></td>
                        <td><?= e($schedule['driver_name']) ?></td>
                        <td><?= e($schedule['departure_date']) ?> <?= e($schedule['departure_time']) ?></td>
                        <td><span class="badge <?= e(status_badge_class($schedule['status'])) ?>"><?= e($schedule['status']) ?></span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" data-edit-form="scheduleForm" data-id="<?= e($schedule['id']) ?>" data-bus-id="<?= e($schedule['bus_id']) ?>" data-driver-id="<?= e($schedule['driver_id']) ?>" data-route-id="<?= e($schedule['route_id']) ?>" data-departure-date="<?= e($schedule['departure_date']) ?>" data-departure-time="<?= e($schedule['departure_time']) ?>" data-arrival-time="<?= e($schedule['arrival_time']) ?>" data-available-seats="<?= e($schedule['available_seats']) ?>" data-fare="<?= e($schedule['fare']) ?>" data-status="<?= e($schedule['status']) ?>">Edit</button>
                            <form method="post" action="<?= url('admin/delete/schedules/' . $schedule['id']) ?>" class="d-inline" data-confirm="Delete this schedule?">
                                <?= \Transport\Core\Csrf::field() ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$schedules): ?>
        <div class="empty-state">
            <i class="bi bi-calendar2-week"></i>
            <h4>No schedules found</h4>
            <p class="text-muted mb-0">Once schedules are created, they will appear here.</p>
        </div>
    <?php endif; ?>
</section>
