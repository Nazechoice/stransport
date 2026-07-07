<?php $pageTitle = 'Register'; ?>
<div class="auth-shell">
    <div class="auth-grid">
        <section class="auth-panel">
            <div class="auth-panel__badge"><i class="bi bi-person-plus"></i> Passenger registration</div>
            <h1 class="auth-panel__title">Create your passenger account in minutes.</h1>
            <p class="auth-panel__copy">
                Register once, then book trips, receive tickets, and manage your travel history from a sleek passenger workspace.
            </p>

            <div class="auth-metrics">
                <div class="auth-metric">
                    <strong>Seat</strong>
                    <small>Book online</small>
                </div>
                <div class="auth-metric">
                    <strong>QR</strong>
                    <small>Boarding pass</small>
                </div>
                <div class="auth-metric">
                    <strong>Trips</strong>
                    <small>Travel history</small>
                </div>
                <div class="auth-metric">
                    <strong>Alerts</strong>
                    <small>Notifications</small>
                </div>
            </div>
        </section>

        <section class="auth-card auth-card--glass">
            <div class="mini-label mb-2">Create account</div>
            <h2 class="h3 mb-2">Passenger registration</h2>
            <p class="text-muted mb-4">Set up your account to start booking journeys.</p>

            <form method="post" action="<?= url('register') ?>" class="row g-3">
                <?= \Transport\Core\Csrf::field() ?>
                <div class="col-md-6">
                    <label class="form-label">Full name</label>
                    <input name="full_name" class="form-control form-control-lg" value="<?= e(old('full_name')) ?>" placeholder="Jane Doe" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input name="phone" class="form-control form-control-lg" value="<?= e(old('phone')) ?>" placeholder="+234..." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg" value="<?= e(old('email')) ?>" placeholder="name@company.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <div class="field-with-action">
                        <input type="password" name="password" id="registerPassword" class="form-control form-control-lg pe-5" placeholder="Create password" required>
                        <button type="button" class="toggle-password" data-toggle-password="registerPassword" aria-label="Toggle password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Confirm password</label>
                    <div class="field-with-action">
                        <input type="password" name="confirm_password" id="registerConfirmPassword" class="form-control form-control-lg pe-5" placeholder="Confirm password" required>
                        <button type="button" class="toggle-password" data-toggle-password="registerConfirmPassword" aria-label="Toggle password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary btn-lg w-100">Create Account</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <span class="text-muted">Already have an account?</span>
                <a href="<?= url('login') ?>" class="text-decoration-none">Login</a>
            </div>
        </section>
    </div>
</div>
