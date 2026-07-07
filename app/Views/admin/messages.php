<?php $pageTitle = 'Contact Messages'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Inbox</div>
        <h1 class="dashboard-hero__title">Review public messages in a calmer interface.</h1>
        <p class="dashboard-hero__copy">
            Track contact submissions and respond with a more readable, modern inbox layout.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Messages</div>
            <div class="mini-value"><?= e(count($messages)) ?> items</div>
            <div class="mini-note">Search, mark read, or close conversations quickly.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">New</span>
            <span class="badge text-bg-success">Read</span>
            <span class="badge text-bg-secondary">Closed</span>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Contact inbox</div>
            <h2 class="page-title mt-1">Messages</h2>
            <p>Messages received from the public landing page.</p>
        </div>
    </div>

    <div class="mb-3">
        <input type="search" class="form-control" data-table-filter="#messagesTable" placeholder="Search messages by name, email, or status">
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="messagesTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($message['full_name']) ?></td>
                        <td><?= e($message['email']) ?></td>
                        <td><?= e($message['phone']) ?></td>
                        <td><?= e($message['message']) ?></td>
                        <td><span class="badge <?= e(status_badge_class($message['status'])) ?>"><?= e($message['status']) ?></span></td>
                        <td class="text-end">
                            <form method="post" action="<?= url('admin/messages/' . $message['id'] . '/status') ?>" class="d-inline">
                                <?= \Transport\Core\Csrf::field() ?>
                                <input type="hidden" name="status" value="read">
                                <button class="btn btn-sm btn-outline-primary">Mark read</button>
                            </form>
                            <form method="post" action="<?= url('admin/messages/' . $message['id'] . '/status') ?>" class="d-inline">
                                <?= \Transport\Core\Csrf::field() ?>
                                <input type="hidden" name="status" value="closed">
                                <button class="btn btn-sm btn-outline-secondary">Close</button>
                            </form>
                            <form method="post" action="<?= url('admin/delete/contact_messages/' . $message['id']) ?>" class="d-inline" data-confirm="Delete this message?">
                                <?= \Transport\Core\Csrf::field() ?>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$messages): ?>
        <div class="empty-state">
            <i class="bi bi-envelope"></i>
            <h4>No messages yet</h4>
            <p class="text-muted mb-0">Public contact submissions will appear here.</p>
        </div>
    <?php endif; ?>
</section>
