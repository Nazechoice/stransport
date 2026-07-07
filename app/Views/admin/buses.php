<?php $pageTitle = 'Manage Buses'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Fleet management</div>
        <h1 class="dashboard-hero__title">Maintain the fleet with a cleaner command view.</h1>
        <p class="dashboard-hero__copy">
            Update buses, assignments, and maintenance status from one streamlined admin screen.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="#busForm">Add bus</a>
            <button class="btn btn-outline-light btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#busEditModal">Open editor</button>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Fleet view</div>
            <div class="mini-value"><?= e(count($buses)) ?> buses</div>
            <div class="mini-note">Keep registrations, images, and driver assignments tidy and searchable.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-success">Active</span>
            <span class="badge text-bg-warning">Maintenance</span>
            <span class="badge text-bg-secondary">Inactive</span>
        </div>
    </aside>
</section>

<section class="panel-card mb-4" id="busForm">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Create bus</div>
            <h2 class="page-title mt-1">Fleet record</h2>
            <p>Add a new vehicle to the active fleet.</p>
        </div>
    </div>

    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#busesTable" placeholder="Search buses by number, registration, or type">
    </div>

    <form method="post" action="<?= url('admin/buses/save') ?>" class="row g-3" enctype="multipart/form-data">
        <?= \Transport\Core\Csrf::field() ?>
        <input type="hidden" name="id" value="">
        <div class="col-md-3">
            <label class="form-label">Bus number</label>
            <input name="bus_number" class="form-control" placeholder="Bus number">
        </div>
        <div class="col-md-3">
            <label class="form-label">Registration number</label>
            <input name="registration_number" class="form-control" placeholder="Registration number">
        </div>
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <input name="bus_type" class="form-control" placeholder="Type">
        </div>
        <div class="col-md-1">
            <label class="form-label">Capacity</label>
            <input name="capacity" type="number" class="form-control" placeholder="Cap">
        </div>
        <div class="col-md-2">
            <label class="form-label">Notes</label>
            <input name="maintenance_notes" class="form-control" placeholder="Maintenance">
        </div>
        <div class="col-md-1">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="maintenance">Maintenance</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Assign driver</label>
            <select name="driver_id" class="form-select">
                <option value="">Assign driver</option>
                <?php foreach ($drivers as $driver): ?>
                    <option value="<?= (int) $driver['id'] ?>"><?= e($driver['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="col-12">
            <button class="btn btn-primary btn-lg">Save bus</button>
        </div>
    </form>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Fleet directory</div>
            <h2 class="page-title mt-1">Buses</h2>
            <p>Review buses and update records as they change.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="busesTable">
            <thead>
                <tr>
                    <th>Bus</th>
                    <th>Reg.</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Driver</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($buses as $bus): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($bus['bus_number']) ?></td>
                        <td><?= e($bus['registration_number']) ?></td>
                        <td><?= e($bus['bus_type']) ?></td>
                        <td><span class="badge text-bg-primary"><?= e($bus['capacity']) ?></span></td>
                        <td>
                            <div class="fw-semibold"><?= e($bus['driver_name'] ?? 'Unassigned') ?></div>
                            <div class="small text-muted"><?= e($bus['driver_phone'] ?? '') ?></div>
                        </td>
                        <td>
                            <?php if (!empty($bus['image'])): ?>
                                <img src="<?= url($bus['image']) ?>" alt="Bus image" style="width:72px;height:44px;object-fit:cover;border-radius:12px;border:1px solid var(--border);">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge <?= e(status_badge_class($bus['status'])) ?>"><?= e($bus['status']) ?></span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-edit-modal="busEditModal" data-id="<?= e($bus['id']) ?>" data-bus-number="<?= e($bus['bus_number']) ?>" data-registration-number="<?= e($bus['registration_number']) ?>" data-bus-type="<?= e($bus['bus_type']) ?>" data-capacity="<?= e($bus['capacity']) ?>" data-maintenance-notes="<?= e($bus['maintenance_notes']) ?>" data-status="<?= e($bus['status']) ?>" data-driver-id="<?= e($bus['driver_id']) ?>">Edit</button>
                            <form method="post" action="<?= url('admin/delete/buses/' . $bus['id']) ?>" class="d-inline" data-confirm="Delete this bus?">
                                <?= \Transport\Core\Csrf::field() ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$buses): ?>
        <div class="empty-state">
            <i class="bi bi-bus-front"></i>
            <h4>No buses found</h4>
            <p class="text-muted mb-0">Create the first fleet record to populate the transport network.</p>
        </div>
    <?php endif; ?>
</section>

<div class="modal fade" id="busEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Edit Bus</h5>
                    <p class="text-muted small mb-0">Update fleet details without leaving the page.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= url('admin/buses/save') ?>" class="row g-3" enctype="multipart/form-data">
                    <?= \Transport\Core\Csrf::field() ?>
                    <input type="hidden" name="id" value="">
                    <div class="col-md-6">
                        <label class="form-label">Bus number</label>
                        <input name="bus_number" class="form-control" placeholder="Bus number">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Registration number</label>
                        <input name="registration_number" class="form-control" placeholder="Registration number">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <input name="bus_type" class="form-control" placeholder="Type">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Capacity</label>
                        <input name="capacity" type="number" class="form-control" placeholder="Capacity">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Maintenance notes</label>
                        <input name="maintenance_notes" class="form-control" placeholder="Maintenance notes">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Driver</label>
                        <select name="driver_id" class="form-select">
                            <option value="">Assign driver</option>
                            <?php foreach ($drivers as $driver): ?>
                                <option value="<?= (int) $driver['id'] ?>"><?= e($driver['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
