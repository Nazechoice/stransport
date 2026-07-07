<?php $pageTitle = 'System Settings'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">System control</div>
        <h1 class="dashboard-hero__title">Adjust platform settings with more breathing room.</h1>
        <p class="dashboard-hero__copy">
            Configure the company profile, booking window, and maintenance mode in a clearer administrative form.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Settings</div>
            <div class="mini-value">System profile</div>
            <div class="mini-note">Use these values to keep public and back-office pages in sync.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-4">
        <div>
            <div class="mini-label">Platform settings</div>
            <h2 class="page-title mt-1">Configuration</h2>
            <p>Configure operational settings for the platform.</p>
        </div>
    </div>

    <form method="post" action="<?= url('admin/settings/save') ?>" class="row g-3" enctype="multipart/form-data">
        <?= \Transport\Core\Csrf::field() ?>
        <div class="col-md-6">
            <label class="form-label">Company name</label>
            <input name="company_name" class="form-control" value="<?= e($settings['company_name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Contact email</label>
            <input name="contact_email" class="form-control" value="<?= e($settings['contact_email'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Contact phone</label>
            <input name="contact_phone" class="form-control" value="<?= e($settings['contact_phone'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Currency symbol</label>
            <input name="currency_symbol" class="form-control" value="<?= e($settings['currency_symbol'] ?? '₦') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Office address</label>
            <input name="office_address" class="form-control" value="<?= e($settings['office_address'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Logo</label>
            <input type="file" name="logo" class="form-control">
            <?php if (!empty($settings['logo'])): ?>
                <div class="form-text mt-2">
                    Current logo:
                    <a href="<?= url($settings['logo']) ?>" target="_blank" rel="noopener">View file</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Timezone</label>
            <input name="timezone" class="form-control" value="<?= e($settings['timezone'] ?? config('app.timezone')) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Booking window (days)</label>
            <input type="number" name="booking_window_days" class="form-control" value="<?= e($settings['booking_window_days'] ?? '30') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Maintenance mode</label>
            <select name="maintenance_mode" class="form-select">
                <option value="off" <?= (($settings['maintenance_mode'] ?? 'off') === 'off') ? 'selected' : '' ?>>Off</option>
                <option value="on" <?= (($settings['maintenance_mode'] ?? 'off') === 'on') ? 'selected' : '' ?>>On</option>
            </select>
        </div>
        <div class="col-12">
            <button class="btn btn-primary btn-lg">Save settings</button>
        </div>
    </form>
</section>
