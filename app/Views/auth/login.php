<?php $pageTitle = 'Login'; ?>
<div class="auth-shell">
    <div class="auth-grid">
        <section class="auth-panel">
            <div class="auth-panel__badge"><i class="bi bi-shield-lock"></i> Secure sign in</div>
            <h1 class="auth-panel__title">Welcome back to your transport workspace.</h1>
            <p class="auth-panel__copy">
                Log in to manage bookings, tickets, fleet operations, or passenger travel with a polished commercial-grade interface.
            </p>

            <div class="auth-metrics">
                <div class="auth-metric">
                    <strong>QR</strong>
                    <small>Fast verification</small>
                </div>
                <div class="auth-metric">
                    <strong>24/7</strong>
                    <small>Operations access</small>
                </div>
                <div class="auth-metric">
                    <strong>Live</strong>
                    <small>Booking insights</small>
                </div>
                <div class="auth-metric">
                    <strong>Safe</strong>
                    <small>Role-based access</small>
                </div>
            </div>
        </section>

        <section class="auth-card auth-card--glass">
            <div class="mini-label mb-2">Sign in</div>
            <h2 class="h3 mb-2">Welcome back</h2>
            <p class="text-muted mb-4">Use your email or admin username and password to continue to the dashboard.</p>

            <form method="post" action="<?= url('login') ?>" class="row g-3">
                <?= \Transport\Core\Csrf::field() ?>
                <div class="col-12">
                    <label class="form-label">Email address or username</label>
                    <input type="text" name="email" class="form-control form-control-lg" value="<?= e(old('email')) ?>" placeholder="admin or name@company.com" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Password</label>
                    <div class="field-with-action">
                        <input type="password" name="password" id="loginPassword" class="form-control form-control-lg pe-5" placeholder="Password" required>
                        <button type="button" class="toggle-password" data-toggle-password="loginPassword" aria-label="Toggle password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="remember_me">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    <a href="<?= url('forgot-password') ?>" class="text-decoration-none">Forgot password?</a>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary btn-lg w-100">Login</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <span class="text-muted">New passenger?</span>
                <a href="<?= url('register') ?>" class="text-decoration-none">Create an account</a>
            </div>
        </section>
    </div>
</div>
