<?php $pageTitle = 'Activity Logs'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Audit trail</div>
        <h1 class="dashboard-hero__title">Monitor system activity with a cleaner audit view.</h1>
        <p class="dashboard-hero__copy">
            Search operational events, security actions, and admin changes in a space that is easier to scan.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Logs</div>
            <div class="mini-value"><?= e(count($logs)) ?> events</div>
            <div class="mini-note">Search by user, module, or action.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-dark">Audit</span>
            <span class="badge text-bg-primary">Security</span>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Activity log</div>
            <h2 class="page-title mt-1">System events</h2>
            <p>System usage and security events.</p>
        </div>
    </div>

    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#logsTable" placeholder="Search logs by user, module, or action">
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="logsTable">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Module</th>
                    <th>Action</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="text-nowrap"><?= e($log['created_at']) ?></td>
                        <td><?= e($log['full_name'] ?? 'System') ?></td>
                        <td><span class="badge text-bg-primary"><?= e($log['module']) ?></span></td>
                        <td><span class="badge text-bg-secondary"><?= e($log['action']) ?></span></td>
                        <td><?= e($log['description']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$logs): ?>
        <div class="empty-state">
            <i class="bi bi-journal-text"></i>
            <h4>No activity logs</h4>
            <p class="text-muted mb-0">User and system events will be recorded here.</p>
        </div>
    <?php endif; ?>
</section>
