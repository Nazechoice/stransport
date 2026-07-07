<?php $pageTitle = 'Notifications'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Alerts</div>
        <h1 class="dashboard-hero__title">Travel notifications in a cleaner feed.</h1>
        <p class="dashboard-hero__copy">
            Stay aware of booking updates, reminders, and travel alerts with a more readable inbox layout.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Notifications</div>
            <div class="mini-value"><?= e(count($notifications)) ?> items</div>
            <div class="mini-note">Review the latest alerts before your next trip.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-3">
        <div>
            <div class="mini-label">Inbox</div>
            <h2 class="page-title mt-1">Notifications</h2>
            <p>Booking updates and travel alerts appear here.</p>
        </div>
    </div>

    <div class="timeline">
        <?php foreach ($notifications as $notification): ?>
            <div class="timeline-item">
                <div>
                    <h6><?= e($notification['title'] ?? 'Notification') ?></h6>
                    <p><?= e($notification['message']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!$notifications): ?>
        <div class="empty-state mt-3">
            <i class="bi bi-bell"></i>
            <h4>No notifications available</h4>
            <p class="text-muted mb-0">Booking updates and travel alerts will appear here.</p>
        </div>
    <?php endif; ?>
</section>
