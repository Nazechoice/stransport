<?php $pageTitle = 'Manage Users'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Identity management</div>
        <h1 class="dashboard-hero__title">Manage staff and passengers in one place.</h1>
        <p class="dashboard-hero__copy">
            Create, edit, and maintain user accounts with a cleaner workflow designed for admin teams.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="#userForm">Add user</a>
            <button class="btn btn-outline-light btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#userEditModal">Open editor</button>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">User base</div>
            <div class="mini-value"><?= e(count($users)) ?> accounts</div>
            <div class="mini-note">Search, filter, and edit records without leaving the page.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Admins</span>
            <span class="badge text-bg-info">Officers</span>
            <span class="badge text-bg-success">Drivers</span>
            <span class="badge text-bg-secondary">Passengers</span>
        </div>
    </aside>
</section>

<section class="panel-card mb-4" id="userForm">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Create user</div>
            <h2 class="page-title mt-1">New account</h2>
            <p>Add new staff or passenger accounts with a single form.</p>
        </div>
    </div>

    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#usersTable" placeholder="Search users by name, email, or phone">
    </div>

    <form method="post" action="<?= url('admin/users/save') ?>" class="row g-3">
        <?= \Transport\Core\Csrf::field() ?>
        <input type="hidden" name="id" value="">
        <div class="col-md-4">
            <label class="form-label">Full name</label>
            <input name="full_name" class="form-control" placeholder="Full name">
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input name="email" class="form-control" placeholder="Email">
        </div>
        <div class="col-md-4">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control" placeholder="Phone">
        </div>
        <div class="col-md-4">
            <label class="form-label">Password</label>
            <input name="password" class="form-control" placeholder="Password (required for new user)">
        </div>
        <div class="col-md-4">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="administrator">Admin</option>
                <option value="ticket_officer">Officer</option>
                <option value="driver">Driver</option>
                <option value="passenger">Passenger</option>
                <option value="super_admin">Super</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>
        <div class="col-12">
            <button class="btn btn-primary btn-lg">Save user</button>
        </div>
    </form>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">User directory</div>
            <h2 class="page-title mt-1">Accounts</h2>
            <p>Select a row to update or remove an account.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="usersTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($user['full_name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><span class="badge <?= e(role_badge_class($user['role'])) ?>"><?= e(str_replace('_', ' ', $user['role'])) ?></span></td>
                        <td><span class="badge <?= e(status_badge_class($user['status'])) ?>"><?= e($user['status']) ?></span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-edit-modal="userEditModal" data-id="<?= e($user['id']) ?>" data-full-name="<?= e($user['full_name']) ?>" data-email="<?= e($user['email']) ?>" data-phone="<?= e($user['phone']) ?>" data-role="<?= e($user['role']) ?>" data-status="<?= e($user['status']) ?>">Edit</button>
                            <form method="post" action="<?= url('admin/delete/users/' . $user['id']) ?>" class="d-inline" data-confirm="Delete this user?">
                                <?= \Transport\Core\Csrf::field() ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$users): ?>
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h4>No users found</h4>
            <p class="text-muted mb-0">Try a different search or add a user to get started.</p>
        </div>
    <?php endif; ?>
</section>

<div class="modal fade" id="userEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Edit User</h5>
                    <p class="text-muted small mb-0">Update account details without leaving the page.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= url('admin/users/save') ?>" class="row g-3">
                    <?= \Transport\Core\Csrf::field() ?>
                    <input type="hidden" name="id" value="">
                    <div class="col-md-6">
                        <label class="form-label">Full name</label>
                        <input name="full_name" class="form-control" placeholder="Full name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input name="email" class="form-control" placeholder="Email">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input name="phone" class="form-control" placeholder="Phone">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input name="password" class="form-control" placeholder="Password (optional for edits)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="administrator">Admin</option>
                            <option value="ticket_officer">Officer</option>
                            <option value="driver">Driver</option>
                            <option value="passenger">Passenger</option>
                            <option value="super_admin">Super</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
