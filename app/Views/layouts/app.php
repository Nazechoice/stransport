<?php

$appName = config('app.name');
$currentUser = auth_user();
$currentRole = $currentUser['role'] ?? null;
$systemLogo = system_setting('logo');

$isLanding = str_starts_with($templateName, 'landing.');
$isAuth = str_starts_with($templateName, 'auth.');
$isBookingPublic = str_starts_with($templateName, 'booking.') && $currentRole === null;
$isPublic = $isLanding || $isAuth || $isBookingPublic;

$pageTitle = $pageTitle ?? match (true) {
    $templateName === 'dashboard.admin' => 'Admin Dashboard',
    $templateName === 'dashboard.passenger' => 'Passenger Dashboard',
    $templateName === 'dashboard.driver' => 'Driver Dashboard',
    $templateName === 'dashboard.officer' => 'Officer Dashboard',
    str_starts_with($templateName, 'admin.') => 'Admin Console',
    str_starts_with($templateName, 'passenger.') => 'Passenger Space',
    str_starts_with($templateName, 'driver.') => 'Driver Space',
    str_starts_with($templateName, 'officer.') => 'Operations Desk',
    str_starts_with($templateName, 'booking.') => 'Booking Studio',
    str_starts_with($templateName, 'reports.') => 'Reports',
    str_starts_with($templateName, 'tickets.') => 'Tickets',
    str_starts_with($templateName, 'landing.') => $appName,
    default => $appName,
};

$pageKicker = match (true) {
    $isLanding => 'Public experience',
    $isAuth => 'Secure access',
    $isBookingPublic => 'Booking experience',
    $currentRole !== null => ucfirst(str_replace('_', ' ', $currentRole)) . ' workspace',
    default => 'Operations workspace',
};

$pageSummary = match (true) {
    $templateName === 'dashboard.admin' => 'Command center for revenue, fleet, bookings, and payments.',
    $templateName === 'dashboard.passenger' => 'Travel hub for reservations, tickets, alerts, and profile management.',
    $templateName === 'dashboard.driver' => 'Driver workspace for trip execution and manifest control.',
    $templateName === 'dashboard.officer' => 'Boarding desk for verification, scanner, and walk-in handling.',
    str_starts_with($templateName, 'admin.') => 'Administrative tools for the transport operations team.',
    str_starts_with($templateName, 'passenger.') => 'Personal travel tools and account controls.',
    str_starts_with($templateName, 'driver.') => 'Assigned trip management and manifest workflows.',
    str_starts_with($templateName, 'officer.') => 'Fast lane tools for ticket checks and boarding.',
    str_starts_with($templateName, 'booking.') => 'Search, seat selection, and booking flow.',
    str_starts_with($templateName, 'reports.') => 'Analytics and export center.',
    str_starts_with($templateName, 'landing.') => 'Premium public booking experience.',
    default => 'Professional transport management workspace.',
};

$sidebarGroups = [];

if (in_array($currentRole, ['super_admin', 'administrator'], true)) {
    $sidebarGroups = [
        [
            'label' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'href' => url('dashboard'), 'match' => ['/dashboard']],
                ['label' => 'Reports', 'icon' => 'bi-bar-chart-line', 'href' => url('admin/reports'), 'match' => ['/admin/reports', '/reports']],
                ['label' => 'Analytics', 'icon' => 'bi-graph-up-arrow', 'href' => url('reports'), 'match' => ['/reports']],
            ],
        ],
        [
            'label' => 'Operations',
            'items' => [
                ['label' => 'Users', 'icon' => 'bi-people', 'href' => url('admin/users'), 'match' => ['/admin/users']],
                ['label' => 'Roles', 'icon' => 'bi-person-gear', 'href' => url('admin/roles'), 'match' => ['/admin/roles']],
                ['label' => 'Passengers', 'icon' => 'bi-person-badge', 'href' => url('admin/users'), 'match' => ['/admin/users']],
                ['label' => 'Drivers', 'icon' => 'bi-truck', 'href' => url('admin/users'), 'match' => ['/admin/users']],
                ['label' => 'Buses', 'icon' => 'bi-bus-front', 'href' => url('admin/buses'), 'match' => ['/admin/buses']],
                ['label' => 'Routes', 'icon' => 'bi-signpost-split', 'href' => url('admin/routes'), 'match' => ['/admin/routes']],
                ['label' => 'Schedules', 'icon' => 'bi-calendar2-week', 'href' => url('admin/schedules'), 'match' => ['/admin/schedules']],
                ['label' => 'Bookings', 'icon' => 'bi-receipt', 'href' => url('admin/bookings'), 'match' => ['/admin/bookings']],
                ['label' => 'Tickets', 'icon' => 'bi-ticket-perforated', 'href' => url('passenger/tickets'), 'match' => ['/passenger/tickets']],
                ['label' => 'Payments', 'icon' => 'bi-cash-stack', 'href' => url('admin/payments'), 'match' => ['/admin/payments']],
            ],
        ],
        [
            'label' => 'System',
            'items' => [
                ['label' => 'Notifications', 'icon' => 'bi-bell', 'href' => url('admin/messages'), 'match' => ['/admin/messages']],
                ['label' => 'Audit Logs', 'icon' => 'bi-journal-text', 'href' => url('admin/logs'), 'match' => ['/admin/logs']],
                ['label' => 'Settings', 'icon' => 'bi-gear', 'href' => url('admin/settings'), 'match' => ['/admin/settings']],
                ['label' => 'Backup', 'icon' => 'bi-hdd-stack', 'href' => url('admin/backup'), 'match' => ['/admin/backup']],
                ['label' => 'Restore', 'icon' => 'bi-arrow-clockwise', 'href' => url('admin/restore'), 'match' => ['/admin/restore']],
            ],
        ],
    ];
} elseif ($currentRole === 'driver') {
    $sidebarGroups = [
        [
            'label' => 'Trips',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'href' => url('dashboard'), 'match' => ['/dashboard']],
                ['label' => 'Assigned Trips', 'icon' => 'bi-truck', 'href' => url('driver/trips'), 'match' => ['/driver/trips']],
                ['label' => 'Manifest', 'icon' => 'bi-clipboard-data', 'href' => url('driver/trips'), 'match' => ['/driver/trips', '/driver/manifest']],
            ],
        ],
    ];
} elseif ($currentRole === 'ticket_officer') {
    $sidebarGroups = [
        [
            'label' => 'Boarding',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'href' => url('dashboard'), 'match' => ['/dashboard']],
                ['label' => 'Verify Ticket', 'icon' => 'bi-qr-code-scan', 'href' => url('officer/verify'), 'match' => ['/officer/verify']],
                ['label' => 'QR Scanner', 'icon' => 'bi-camera-video', 'href' => url('officer/scanner'), 'match' => ['/officer/scanner']],
                ['label' => 'Walk-in Booking', 'icon' => 'bi-person-plus', 'href' => url('officer/walkin'), 'match' => ['/officer/walkin']],
                ['label' => 'Boarding List', 'icon' => 'bi-list-check', 'href' => url('officer/boarding-list'), 'match' => ['/officer/boarding-list']],
            ],
        ],
    ];
} elseif ($currentRole === 'passenger') {
    $sidebarGroups = [
        [
            'label' => 'Travel',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'href' => url('dashboard'), 'match' => ['/dashboard']],
                ['label' => 'Search Journey', 'icon' => 'bi-search', 'href' => url('journey/search'), 'match' => ['/journey/search']],
                ['label' => 'Bookings', 'icon' => 'bi-receipt', 'href' => url('bookings/history'), 'match' => ['/bookings/history']],
                ['label' => 'My Tickets', 'icon' => 'bi-ticket-perforated', 'href' => url('passenger/tickets'), 'match' => ['/passenger/tickets']],
                ['label' => 'Travel History', 'icon' => 'bi-clock-history', 'href' => url('passenger/travel-history'), 'match' => ['/passenger/travel-history']],
            ],
        ],
        [
            'label' => 'Account',
            'items' => [
                ['label' => 'Profile', 'icon' => 'bi-person-badge', 'href' => url('passenger/profile'), 'match' => ['/passenger/profile']],
                ['label' => 'Notifications', 'icon' => 'bi-bell', 'href' => url('passenger/notifications'), 'match' => ['/passenger/notifications']],
                ['label' => 'Password', 'icon' => 'bi-key', 'href' => url('passenger/password'), 'match' => ['/passenger/password']],
            ],
        ],
    ];
}

$success = \Transport\Core\Session::get('success');
\Transport\Core\Session::remove('success');
$error = \Transport\Core\Session::get('error');
\Transport\Core\Session::remove('error');
$errors = validation_errors();

$brandLetters = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $appName) ?: 'PBT', 0, 2));
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2563eb">
    <title><?= e($pageTitle) ?> | <?= e($appName) ?></title>
    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('pbt-theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = savedTheme || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', theme);
            } catch (error) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body class="<?= $isPublic ? 'marketing-shell' : 'app-shell' ?>" data-base-url="<?= e(rtrim(url(''), '/')) ?>">
<div class="page-backdrop" aria-hidden="true"></div>

<?php if ($isPublic): ?>
    <header class="marketing-header sticky-top">
        <nav class="navbar navbar-expand-lg container marketing-nav">
                <a class="brand-marketing" href="<?= url('') ?>">
                    <?php if (!empty($systemLogo)): ?>
                        <span class="brand-mark brand-mark--image">
                            <img src="<?= url($systemLogo) ?>" alt="<?= e($appName) ?> logo">
                        </span>
                    <?php else: ?>
                        <span class="brand-mark"><?= e($brandLetters) ?></span>
                    <?php endif; ?>
                    <span class="brand-copy">
                        <strong><?= e($appName) ?></strong>
                        <small>Premium transport software</small>
                    </span>
                </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#marketingNav" aria-controls="marketingNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="marketingNav">
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <a class="nav-link" href="<?= url('journey/search') ?>">Search Journey</a>
                    <a class="nav-link" href="<?= url('about') ?>">About</a>
                    <a class="nav-link" href="<?= url('routes') ?>">Routes</a>
                    <a class="nav-link" href="<?= url('vehicles') ?>">Vehicles</a>
                    <a class="nav-link" href="<?= url('services') ?>">Services</a>
                    <a class="nav-link" href="<?= url('contact') ?>">Contact</a>
                    <a class="nav-link" href="<?= url('register') ?>">Register</a>
                    <a class="nav-link" href="<?= url('login') ?>">Login</a>
                    <a class="btn btn-primary ms-lg-2" href="<?= url('journey/search') ?>">Book Now</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="marketing-main">
        <div class="marketing-main__inner">
            <?php if ($errors): ?>
                <div class="alert alert-warning alert-soft border-0 rounded-4 shadow-sm">
                    <?php foreach ($errors as $message): ?>
                        <div><?= e($message) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success || $error): ?>
                <div class="toast-stack position-fixed top-0 end-0 p-3 p-md-4">
                    <div class="toast shell-toast <?= $success ? 'toast-success' : 'toast-danger' ?>" role="status" aria-live="polite" aria-atomic="true" data-delay="4200">
                        <div class="d-flex align-items-center">
                            <div class="toast-body fw-semibold"><?= e($success ?: $error) ?></div>
                            <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </main>

    <footer class="marketing-footer">
        <div class="container py-4 text-center text-white-50">
            &copy; <?= date('Y') ?> <?= e($appName) ?>. Designed for premium transport operations.
        </div>
    </footer>
<?php else: ?>
    <div class="workspace-shell">
        <aside class="workspace-sidebar" id="sidebar">
            <div class="workspace-sidebar__top">
                <a class="brand-marketing brand-marketing--sidebar" href="<?= url('dashboard') ?>">
                    <?php if (!empty($systemLogo)): ?>
                        <span class="brand-mark brand-mark--image">
                            <img src="<?= url($systemLogo) ?>" alt="<?= e($appName) ?> logo">
                        </span>
                    <?php else: ?>
                        <span class="brand-mark"><?= e($brandLetters) ?></span>
                    <?php endif; ?>
                    <span class="brand-copy">
                        <strong><?= e($appName) ?></strong>
                        <small>Operations suite</small>
                    </span>
                </a>
                <button class="btn btn-icon btn-ghost d-xl-none" type="button" id="sidebarClose" aria-label="Close sidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="workspace-sidebar__summary">
                <div class="workspace-sidebar__kicker"><?= e($pageKicker) ?></div>
                <h2 class="workspace-sidebar__title"><?= e($pageTitle) ?></h2>
                <p class="workspace-sidebar__copy"><?= e($pageSummary) ?></p>
            </div>

            <nav class="workspace-nav">
                <?php foreach ($sidebarGroups as $group): ?>
                    <section class="workspace-nav__section">
                        <div class="workspace-nav__label"><?= e($group['label']) ?></div>
                        <div class="workspace-nav__links">
                            <?php foreach ($group['items'] as $item): ?>
                                <a href="<?= e($item['href']) ?>" class="workspace-nav__link <?= route_is($item['match']) ? 'active' : '' ?>">
                                    <i class="bi <?= e($item['icon']) ?>"></i>
                                    <span><?= e($item['label']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </nav>

            <div class="workspace-sidebar__bottom">
                <?php if ($currentUser): ?>
                    <div class="workspace-user">
                        <div class="avatar-circle"><?= e(strtoupper(substr((string) $currentUser['name'], 0, 1))) ?></div>
                        <div class="workspace-user__meta">
                            <strong><?= e($currentUser['name']) ?></strong>
                            <small><?= e(str_replace('_', ' ', (string) $currentRole)) ?></small>
                        </div>
                    </div>
                <?php endif; ?>
                <a href="<?= url('logout') ?>" class="workspace-nav__link workspace-nav__link--danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <div class="workspace-body">
            <header class="workspace-topbar">
                <div class="workspace-topbar__left">
                    <button class="btn btn-icon btn-ghost" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="bi bi-layout-sidebar-inset"></i>
                    </button>
                    <div class="workspace-heading">
                        <span class="workspace-heading__kicker"><?= e($pageKicker) ?></span>
                        <div class="workspace-heading__title"><?= e($pageTitle) ?></div>
                        <p class="workspace-heading__copy d-none d-md-block"><?= e($pageSummary) ?></p>
                    </div>
                </div>

                <div class="workspace-topbar__center">
                    <label class="topbar-search" for="globalSearch">
                        <i class="bi bi-search"></i>
                        <input type="search" id="globalSearch" placeholder="Search visible table or form">
                    </label>
                </div>

                <div class="workspace-topbar__right">
                    <button class="btn btn-icon btn-ghost" id="themeToggle" type="button" aria-label="Toggle theme">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                    <button class="btn btn-icon btn-ghost d-none d-md-inline-flex" type="button" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                    </button>
                    <?php if ($currentUser): ?>
                        <div class="dropdown">
                            <button class="profile-chip btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="avatar-circle avatar-circle--sm"><?= e(strtoupper(substr((string) $currentUser['name'], 0, 1))) ?></span>
                                <span class="profile-chip__meta d-none d-lg-flex">
                                    <strong><?= e($currentUser['name']) ?></strong>
                                    <small><?= e(str_replace('_', ' ', (string) $currentRole)) ?></small>
                                </span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shell-dropdown shadow-lg border-0 rounded-4">
                                <?php if ($currentRole === 'passenger'): ?>
                                    <li><a class="dropdown-item" href="<?= url('passenger/profile') ?>">Profile</a></li>
                                    <li><a class="dropdown-item" href="<?= url('passenger/password') ?>">Password</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= url('dashboard') ?>">Dashboard</a></li>
                                <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>">Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <div class="workspace-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

            <main class="workspace-main">
                <div class="workspace-main__inner">
                    <?php if ($errors): ?>
                        <div class="alert alert-warning alert-soft border-0 rounded-4 shadow-sm">
                            <?php foreach ($errors as $message): ?>
                                <div><?= e($message) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success || $error): ?>
                        <div class="toast-stack position-fixed top-0 end-0 p-3 p-md-4">
                            <div class="toast shell-toast <?= $success ? 'toast-success' : 'toast-danger' ?>" role="status" aria-live="polite" aria-atomic="true" data-delay="4200">
                                <div class="d-flex align-items-center">
                                    <div class="toast-body fw-semibold"><?= e($success ?: $error) ?></div>
                                    <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?= $content ?>
                </div>
            </main>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
