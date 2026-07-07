<?php
$pageTitle = 'Contact';
$contactCards = [
    ['icon' => 'bi-envelope', 'label' => 'Email', 'value' => 'support@transport.test'],
    ['icon' => 'bi-telephone', 'label' => 'Phone', 'value' => '+234 800 000 0000'],
    ['icon' => 'bi-geo-alt', 'label' => 'Office', 'value' => 'Transport Headquarters, Lagos, Nigeria'],
];
?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Contact page</div>
        <h1 class="dashboard-hero__title">Reach the transport team directly.</h1>
        <p class="dashboard-hero__copy">
            Send a support request, ask about bookings, or request help with a ticket, route, or account issue.
        </p>
        <div class="dashboard-hero__actions">
            <a class="btn btn-light btn-lg" href="<?= url('journey/search') ?>">Search journey</a>
            <a class="btn btn-outline-light btn-lg" href="<?= url('services') ?>">Our services</a>
        </div>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">System scale</div>
            <div class="mini-value"><?= e($stats['bookings']) ?> bookings</div>
            <div class="mini-note">Your message reaches the same team that manages the live operation.</div>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Contact details</div>
                <h2 class="page-title mt-1">How to reach us</h2>
                <p>Use the channels below for booking support and transport enquiries.</p>
            </div>
        </div>

        <div class="dashboard-grid dashboard-grid--three">
            <?php foreach ($contactCards as $card): ?>
                <div class="feature-card">
                    <i class="bi <?= e($card['icon']) ?> feature-icon"></i>
                    <h5><?= e($card['label']) ?></h5>
                    <p class="feature-copy mb-0"><?= e($card['value']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Send message</div>
                <h2 class="page-title mt-1">Contact form</h2>
                <p>We’ll store your request in the admin inbox.</p>
            </div>
        </div>

        <form method="post" action="<?= url('contact') ?>" class="row g-3">
            <?= \Transport\Core\Csrf::field() ?>
            <div class="col-md-6">
                <label class="form-label">Full name</label>
                <input name="full_name" value="<?= e(old('full_name')) ?>" class="form-control" placeholder="Full name" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?= e(old('email')) ?>" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input name="phone" value="<?= e(old('phone')) ?>" class="form-control" placeholder="Phone">
            </div>
            <div class="col-12">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="5" placeholder="Your message" required><?= e(old('message')) ?></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary btn-lg">Send Message</button>
            </div>
        </form>
    </div>
</section>
