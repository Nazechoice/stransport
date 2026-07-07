<?php $pageTitle = 'Password'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Security</div>
        <h1 class="dashboard-hero__title">Change your password from a dedicated security screen.</h1>
        <p class="dashboard-hero__copy">
            Keep your account secure with a new password that is easy to manage and hard to guess.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Account safety</div>
            <div class="mini-value">Protected login</div>
            <div class="mini-note">Use a strong password and update it regularly if you share devices.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-4">
        <div>
            <div class="mini-label">Security</div>
            <h2 class="page-title mt-1">Change password</h2>
            <p>Choose a fresh password to keep your account secure.</p>
        </div>
    </div>

    <form method="post" action="<?= url('passenger/password') ?>" class="row g-3">
        <?= \Transport\Core\Csrf::field() ?>
        <div class="col-md-4">
            <label class="form-label">Current password</label>
            <input type="password" name="current_password" class="form-control form-control-lg" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">New password</label>
            <input type="password" name="new_password" class="form-control form-control-lg" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Confirm password</label>
            <input type="password" name="confirm_password" class="form-control form-control-lg" required>
        </div>
        <div class="col-12">
            <button class="btn btn-primary btn-lg">Update password</button>
        </div>
    </form>
</section>
