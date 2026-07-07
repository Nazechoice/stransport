<?php $pageTitle = 'Reset Password'; ?>
<div class="auth-shell">
    <div class="auth-grid">
        <section class="auth-panel">
            <div class="auth-panel__badge"><i class="bi bi-shield-check"></i> Password reset</div>
            <h1 class="auth-panel__title">Set a new secure password.</h1>
            <p class="auth-panel__copy">
                Choose a strong password and continue back into the platform with uninterrupted access.
            </p>
        </section>

        <section class="auth-card auth-card--glass">
            <div class="mini-label mb-2">Reset password</div>
            <h2 class="h3 mb-2">Choose a new password</h2>
            <p class="text-muted mb-4">This reset link is tied to the email address below.</p>

            <form method="post" action="<?= url('reset-password') ?>" class="row g-3">
                <?= \Transport\Core\Csrf::field() ?>
                <input type="hidden" name="email" value="<?= e($email) ?>">
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="col-12">
                    <label class="form-label">New password</label>
                    <div class="field-with-action">
                        <input type="password" name="password" id="resetPassword" class="form-control form-control-lg pe-5" placeholder="New password" required>
                        <button type="button" class="toggle-password" data-toggle-password="resetPassword" aria-label="Toggle password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Confirm password</label>
                    <div class="field-with-action">
                        <input type="password" name="confirm_password" id="resetConfirmPassword" class="form-control form-control-lg pe-5" placeholder="Confirm password" required>
                        <button type="button" class="toggle-password" data-toggle-password="resetConfirmPassword" aria-label="Toggle password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary btn-lg w-100">Update Password</button>
                </div>
            </form>
        </section>
    </div>
</div>
