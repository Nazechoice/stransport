<?php $pageTitle = 'Profile'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Account</div>
        <h1 class="dashboard-hero__title">Keep your passenger profile up to date.</h1>
        <p class="dashboard-hero__copy">
            Update your contact details and photo so boarding and support teams can identify you quickly.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Profile</div>
            <div class="mini-value"><?= e($user['full_name']) ?></div>
            <div class="mini-note">A current phone number helps with booking alerts and trip support.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-4">
        <div>
            <div class="mini-label">Passenger profile</div>
            <h2 class="page-title mt-1">Update profile</h2>
            <p>Keep your passenger details current for smoother travel support.</p>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-3">
            <div class="info-card h-100">
                <div class="mini-label mb-2">Photo</div>
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?= url($user['photo']) ?>" alt="Profile photo" class="rounded-4 w-100" style="aspect-ratio: 1 / 1; object-fit: cover;">
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-person-circle"></i>
                        <h4>No photo yet</h4>
                        <p class="text-muted mb-0">Upload a clear profile photo.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-9">
            <form method="post" action="<?= url('passenger/profile') ?>" enctype="multipart/form-data" class="row g-3">
                <?= \Transport\Core\Csrf::field() ?>
                <div class="col-md-6">
                    <label class="form-label">Full name</label>
                    <input name="full_name" class="form-control form-control-lg" value="<?= e($user['full_name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input name="phone" class="form-control form-control-lg" value="<?= e($user['phone']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control form-control-lg" value="<?= e($user['email']) ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control form-control-lg">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary btn-lg">Update profile</button>
                </div>
            </form>
        </div>
    </div>
</section>
