<?php $pageTitle = 'Backup & Restore'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Data protection</div>
        <h1 class="dashboard-hero__title">Protect the database with a polished backup page.</h1>
        <p class="dashboard-hero__copy">
            Generate SQL backups before maintenance windows and keep recovery actions easy to find.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Backup</div>
            <div class="mini-value">SQL export</div>
            <div class="mini-note">Download a live backup snapshot when you need one.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-4">
        <div>
            <div class="mini-label">Backup and restore</div>
            <h2 class="page-title mt-1">Database export</h2>
            <p>Generate a SQL backup of the live database.</p>
        </div>
    </div>

    <form method="post" action="<?= url('admin/backup') ?>" data-confirm="Download a database backup now?">
        <?= \Transport\Core\Csrf::field() ?>
        <button class="btn btn-primary btn-lg">Download SQL backup</button>
    </form>
</section>
