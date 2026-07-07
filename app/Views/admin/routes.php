<?php $pageTitle = 'Manage Routes'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Network design</div>
        <h1 class="dashboard-hero__title">Shape routes with a clearer control surface.</h1>
        <p class="dashboard-hero__copy">
            Define origins, destinations, fare rules, and service details in a cleaner, more navigable layout.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="#routeForm">Add route</a>
            <button class="btn btn-outline-light btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#routeModal">Open editor</button>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Route catalog</div>
            <div class="mini-value"><?= e(count($routes)) ?> routes</div>
            <div class="mini-note">Search, edit, and sort route records from the table below.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-success">Active</span>
            <span class="badge text-bg-secondary">Inactive</span>
        </div>
    </aside>
</section>

<section class="panel-card mb-4" id="routeSection">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Create route</div>
            <h2 class="page-title mt-1">Route record</h2>
            <p>Add a route and immediately make it available for scheduling.</p>
        </div>
    </div>

    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#routesTable" placeholder="Search routes by origin or destination">
    </div>

    <form method="post" action="<?= url('admin/routes/save') ?>" id="routeForm" class="row g-3">
        <?= \Transport\Core\Csrf::field() ?>
        <input type="hidden" name="id" value="">
        <div class="col-md-2">
            <label class="form-label">Origin</label>
            <input name="origin" class="form-control" placeholder="Origin">
        </div>
        <div class="col-md-2">
            <label class="form-label">Destination</label>
            <input name="destination" class="form-control" placeholder="Destination">
        </div>
        <div class="col-md-3">
            <label class="form-label">Stops</label>
            <input name="stops" class="form-control" placeholder="Stops">
        </div>
        <div class="col-md-2">
            <label class="form-label">Distance (km)</label>
            <input name="distance_km" class="form-control" placeholder="Distance">
        </div>
        <div class="col-md-1">
            <label class="form-label">Minutes</label>
            <input name="estimated_minutes" class="form-control" placeholder="Minutes">
        </div>
        <div class="col-md-1">
            <label class="form-label">Fare</label>
            <input name="fare" class="form-control" placeholder="Fare">
        </div>
        <div class="col-md-1">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="col-12">
            <button class="btn btn-primary btn-lg">Save route</button>
        </div>
    </form>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Route directory</div>
            <h2 class="page-title mt-1">Routes</h2>
            <p>Review and maintain route pricing and service details.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="routesTable">
            <thead>
                <tr>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Stops</th>
                    <th>Distance</th>
                    <th>Fare</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $route): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($route['origin']) ?></td>
                        <td><?= e($route['destination']) ?></td>
                        <td><?= e($route['stops']) ?></td>
                        <td><?= e($route['distance_km']) ?> km</td>
                        <td><?= money($route['fare']) ?></td>
                        <td><span class="badge <?= e(status_badge_class($route['status'])) ?>"><?= e($route['status']) ?></span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" data-edit-form="routeForm" data-id="<?= e($route['id']) ?>" data-origin="<?= e($route['origin']) ?>" data-destination="<?= e($route['destination']) ?>" data-stops="<?= e($route['stops']) ?>" data-distance-km="<?= e($route['distance_km']) ?>" data-estimated-minutes="<?= e($route['estimated_minutes']) ?>" data-fare="<?= e($route['fare']) ?>" data-status="<?= e($route['status']) ?>">Edit</button>
                            <form method="post" action="<?= url('admin/delete/routes/' . $route['id']) ?>" class="d-inline" data-confirm="Delete this route?">
                                <?= \Transport\Core\Csrf::field() ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$routes): ?>
        <div class="empty-state">
            <i class="bi bi-signpost-split"></i>
            <h4>No routes found</h4>
            <p class="text-muted mb-0">Create a route to start scheduling trips.</p>
        </div>
    <?php endif; ?>
</section>
