<?php $pageTitle = 'Forgot Password'; ?>
<div class="auth-shell">
    <div class="auth-grid">
        <section class="auth-panel">
            <div class="auth-panel__badge"><i class="bi bi-key"></i> Account recovery</div>
            <h1 class="auth-panel__title">Recover access with a polished reset flow.</h1>
            <p class="auth-panel__copy">
                Enter your email address and we will generate a secure reset link for your account.
            </p>
            <div class="auth-metrics">
                <div class="auth-metric">
                    <strong>60m</strong>
                    <small>Reset window</small>
                </div>
                <div class="auth-metric">
                    <strong>Safe</strong>
                    <small>Token based</small>
                </div>
            </div>
        </section>

        <section class="auth-card auth-card--glass">
            <div class="mini-label mb-2">Reset access</div>
            <h2 class="h3 mb-2">Forgot password</h2>
            <p class="text-muted mb-4">We will generate a reset link for the email address you provide.</p>

            <form method="post" action="<?= url('forgot-password') ?>" class="row g-3">
                <?= \Transport\Core\Csrf::field() ?>
                <div class="col-12">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="name@company.com" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary btn-lg w-100">Generate Reset Link</button>
                </div>
            </form>

            <?php if ($link = \Transport\Core\Session::get('reset_link')): ?>
                <div class="mt-4 alert alert-info border-0 rounded-4 mb-0">
                    <div class="fw-semibold mb-1">Reset link generated</div>
                    <a href="<?= e($link) ?>"><?= e($link) ?></a>
                    <?php \Transport\Core\Session::remove('reset_link'); ?>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="<?= url('login') ?>" class="text-decoration-none">Back to login</a>
            </div>
        </section>
    </div>
</div>
