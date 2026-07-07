<?php

$topRoutes = array_slice($popularRoutes, 0, 6);
$featuredRoutes = array_slice($routes, 0, 4);
?>

<section class="landing-section">
    <div class="hero-section align-items-center">
        <div class="hero-panel">
            <div class="hero-kicker">Premium transport platform</div>
            <h1 class="hero-title">Book seats, manage trips, and run operations with confidence.</h1>
            <p class="hero-copy">
                A modern transport management experience for passengers, officers, drivers, and administrators.
                Search journeys, issue QR tickets, monitor revenue, and keep fleet operations beautifully organized.
            </p>
            <div class="hero-actions">
                <a href="<?= url('journey/search') ?>" class="btn btn-light btn-lg px-4">Search Journey</a>
                <a href="<?= url('register') ?>" class="btn btn-outline-light btn-lg px-4">Create Account</a>
            </div>

            <div class="row g-3 mt-4">
                <div class="col-6 col-lg-4">
                    <div class="hero-card-mini">
                        <div class="journey-pill mb-2"><i class="bi bi-shield-check"></i> Secure</div>
                        <div class="small text-white-75">Role-based access, CSRF protection, and audit trails.</div>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="hero-card-mini">
                        <div class="journey-pill mb-2"><i class="bi bi-qr-code-scan"></i> QR Boarding</div>
                        <div class="small text-white-75">Fast ticket verification for boarding desks and scanners.</div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="hero-card-mini">
                        <div class="journey-pill mb-2"><i class="bi bi-speedometer2"></i> Analytics</div>
                        <div class="small text-white-75">Bookings, payments, and route performance in one place.</div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="hero-side">
            <div class="hero-search-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div class="mini-label">Journey Search</div>
                        <h2 class="h4 mb-0 mt-1">Find the best trip</h2>
                    </div>
                    <span class="badge text-bg-primary-subtle text-primary border border-primary-subtle">Live</span>
                </div>

                <form method="get" action="<?= url('journey/search') ?>" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Origin</label>
                        <input name="origin" class="form-control form-control-lg" placeholder="e.g. Lagos">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Destination</label>
                        <input name="destination" class="form-control form-control-lg" placeholder="e.g. Abuja">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Travel Date</label>
                        <input type="date" name="date" class="form-control form-control-lg">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary btn-lg w-100">Search Available Trips</button>
                    </div>
                </form>
            </div>

            <div class="bus-scene">
                <div class="route-line one"></div>
                <div class="route-line two"></div>
                <div class="bus-body">
                    <div class="bus-window one"></div>
                    <div class="bus-window two"></div>
                    <div class="bus-window three"></div>
                    <div class="bus-window four"></div>
                    <div class="bus-window five"></div>
                    <div class="bus-wheel left"></div>
                    <div class="bus-wheel right"></div>
                </div>
            </div>
        </aside>
    </div>
</section>

<section class="landing-section pt-0">
    <div class="dashboard-grid dashboard-grid--four">
        <?php foreach ($stats as $label => $value): ?>
            <div class="stat-card landing-stat">
                <div class="stat-value"><?= e(is_float($value) ? money($value) : $value) ?></div>
                <div class="stat-label text-capitalize"><?= e(str_replace('_', ' ', $label)) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="landing-section">
    <div class="section-heading mb-4">
        <div>
            <div class="mini-label">Popular routes</div>
            <h2 class="page-title mt-1">High-demand corridors</h2>
            <p>See which city pairs are moving the most bookings today.</p>
        </div>
        <a href="<?= url('journey/search') ?>" class="btn btn-outline-primary">Explore journeys</a>
    </div>

    <div class="dashboard-grid dashboard-grid--three">
        <?php foreach ($topRoutes as $route): ?>
            <div class="route-card">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <div class="journey-pill mb-3"><i class="bi bi-signpost-split"></i> Route</div>
                        <h3 class="route-title mb-2"><?= e($route['origin']) ?> to <?= e($route['destination']) ?></h3>
                        <div class="route-meta"><?= e($route['bookings']) ?> bookings on this corridor</div>
                    </div>
                    <i class="bi bi-arrow-up-right-circle fs-3 text-primary"></i>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="landing-section pt-0">
    <div class="dashboard-grid dashboard-grid--two">
        <div class="panel-card">
            <div class="section-heading mb-3">
                <div>
                    <div class="mini-label">Why choose us</div>
                    <h2 class="page-title mt-1">A cleaner workflow for every role</h2>
                    <p>Passengers, officers, drivers, and admins each get a focused workspace.</p>
                </div>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div>
                        <h6>Book faster</h6>
                        <p>Search routes, compare schedules, choose a seat, and confirm in one flow.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div>
                        <h6>Board with confidence</h6>
                        <p>QR verification and manifest views make ticket checks quick and reliable.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div>
                        <h6>Run operations clearly</h6>
                        <p>Dashboards, analytics, logs, and finance tools stay organized and readable.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="section-heading mb-3">
                <div>
                    <div class="mini-label">Featured services</div>
                    <h2 class="page-title mt-1">Built for daily transport operations</h2>
                    <p>Everything is structured around the real work your team performs.</p>
                </div>
            </div>

            <div class="dashboard-grid dashboard-grid--two">
                <div class="feature-card">
                    <i class="bi bi-ticket-perforated feature-icon"></i>
                    <h5>Ticketing</h5>
                    <p class="feature-copy">Issue, print, and download professional boarding passes.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-qr-code feature-icon"></i>
                    <h5>Verification</h5>
                    <p class="feature-copy">Officer tools for scanning and confirming ticket validity.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-bar-chart-line feature-icon"></i>
                    <h5>Analytics</h5>
                    <p class="feature-copy">Revenue, route demand, and fleet performance at a glance.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-bus-front feature-icon"></i>
                    <h5>Fleet Control</h5>
                    <p class="feature-copy">Routes, buses, schedules, and driver assignments stay in sync.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="landing-section">
    <div class="section-heading mb-4">
        <div>
            <div class="mini-label">Testimonials</div>
            <h2 class="page-title mt-1">Teams trust the experience</h2>
            <p>Clear workflows reduce friction for passengers and staff alike.</p>
        </div>
    </div>

    <div class="dashboard-grid dashboard-grid--three">
        <div class="testimonial-card">
            <div class="journey-pill mb-3"><i class="bi bi-quote"></i> Passenger</div>
            <p class="testimonial-copy mb-3">Booking a seat took less than two minutes and the ticket screen feels premium.</p>
            <strong>Chinwe A.</strong>
        </div>
        <div class="testimonial-card">
            <div class="journey-pill mb-3"><i class="bi bi-quote"></i> Driver</div>
            <p class="testimonial-copy mb-3">The manifest view is clean and makes departure checks much easier.</p>
            <strong>Abdullahi T.</strong>
        </div>
        <div class="testimonial-card">
            <div class="journey-pill mb-3"><i class="bi bi-quote"></i> Admin</div>
            <p class="testimonial-copy mb-3">Analytics, bookings, and payments are now much easier to monitor from one place.</p>
            <strong>Grace O.</strong>
        </div>
    </div>
</section>

<section class="landing-section pt-0">
    <div class="panel-card">
        <div class="section-heading mb-4">
            <div>
                <div class="mini-label">Trusted by teams</div>
                <h2 class="page-title mt-1">Partner companies and transport operators</h2>
                <p>Stylized placeholders that mirror a premium SaaS partner strip.</p>
            </div>
        </div>

        <div class="partner-strip">
            <div class="partner-chip">Transit One</div>
            <div class="partner-chip">MetroLink</div>
            <div class="partner-chip">RouteLine</div>
            <div class="partner-chip">NorthStar</div>
            <div class="partner-chip">SwiftMove</div>
            <div class="partner-chip">BlueLine</div>
        </div>
    </div>
</section>

<section class="landing-section">
    <div class="dashboard-grid dashboard-grid--two">
        <div class="panel-card">
            <div class="mini-label mb-2">Frequently asked</div>
            <h2 class="page-title mb-3">Common questions</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq1">How do I book a ticket?</button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Search for a journey, pick a schedule, select a seat, and confirm the booking.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">Can I print my ticket?</button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes. Every ticket has print and download actions on the ticket screen.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq3">Does the system support staff roles?</button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes. Separate dashboards exist for administrators, officers, drivers, and passengers.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="mini-label mb-2">Contact</div>
            <h2 class="page-title mb-3">Send a message</h2>
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
    </div>
</section>

<section class="landing-section pt-0">
    <div class="panel-card d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="mini-label">Get started</div>
            <h2 class="page-title mt-1 mb-2">Ready to move your transport operation forward?</h2>
            <p class="mb-0 text-muted">Create an account, search a route, and experience the redesigned workflow.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= url('journey/search') ?>" class="btn btn-primary btn-lg">Search Journey</a>
            <a href="<?= url('register') ?>" class="btn btn-outline-primary btn-lg">Create Account</a>
        </div>
    </div>
</section>
