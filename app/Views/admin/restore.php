<?php $pageTitle = 'Restore Backup'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Database recovery</div>
        <h1 class="dashboard-hero__title">Restore backups from a clearer recovery screen.</h1>
        <p class="dashboard-hero__copy">
            Upload a SQL backup file when you need to recover the live database after maintenance or migration.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Restore</div>
            <div class="mini-value">SQL upload</div>
            <div class="mini-note">Always confirm the file before restoring production data.</div>
        </div>
    </aside>
</section>

<section class="panel-card">
    <div class="section-heading mb-4">
        <div>
            <div class="mini-label">Recovery</div>
            <h2 class="page-title mt-1">Restore backup</h2>
            <p>Upload a SQL backup file to restore the database.</p>
        </div>
    </div>

    <form method="post" action="<?= url('admin/restore') ?>" enctype="multipart/form-data" class="row g-3" data-confirm="Restoring will overwrite the current database state. Continue?">
        <?= \Transport\Core\Csrf::field() ?>
        <div class="col-md-8">
            <input type="file" name="backup_file" accept=".sql" class="form-control form-control-lg" required>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary btn-lg w-100">Restore database</button>
        </div>
    </form>
</section>
