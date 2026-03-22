<?php

/**
 * About page template.
 *
 * Expected variables (set by index.php before including this file):
 *   $i18n I18n
 */

declare(strict_types=1);

?>

<main class="container py-5" style="max-width:900px">
    <h1 class="fw-bold mb-4"><?php echo htmlspecialchars($i18n->t('about.heading'), ENT_QUOTES, 'UTF-8'); ?></h1>

    <!-- ── About WP Plugin Insight ───────────────────────────────────────── -->
    <section class="mb-5" aria-labelledby="about-project">
        <h2 class="h4 fw-semibold mb-3" id="about-project">About WP Plugin Insight</h2>
        <p>
            WP Plugin Insight is an AI-assisted platform that analyzes WordPress plugins at the code
            level to provide objective insights into quality, compatibility, security, and long-term
            maintainability. Instead of relying on developer-declared metadata, it scans real plugin
            code to detect deprecated APIs, risky patterns, PHP and WordPress version requirements,
            internationalization readiness, external connections, and other hidden behaviors.
        </p>
        <p>
            Beyond analysis, WP Plugin Insight powers an alternative, user-centric plugin discovery
            experience. Plugins can be searched and filtered by real technical criteria — such as
            PHP&nbsp;8.3 compatibility, absence of deprecated APIs, or translation completeness — and
            ranked by composite quality signals rather than download counts alone.
        </p>

        <h3 class="h5 fw-semibold mt-4 mb-2">History</h3>
        <p>
            WP Plugin Insight was born at the
            <a href="https://hackathon.cloudfest.com/project/wp-plugin-insight/"
               target="_blank"
               rel="noopener noreferrer">CloudFest Hackathon 2026</a>
            (March&nbsp;20–22,&nbsp;2026), held at Europa-Park in Rust, Germany. In 48&nbsp;hours,
            a cross-discipline team spanning PHP, AI/LLM, static analysis, search engineering,
            frontend, DevOps, and security built a working MVP of the full analysis pipeline.
        </p>
        <p>The hackathon delivered:</p>
        <ul>
            <li>A working plugin analysis engine detecting quality, compatibility, and deprecated API usage</li>
            <li>Automatic inference of minimum required PHP and WordPress versions from real code</li>
            <li>Human-readable analysis reports with structured JSON output</li>
            <li>A REST API for plugin submission and report retrieval</li>
            <li>A live AI-assisted capability for natural-language plugin queries</li>
        </ul>
        <p>
            The project is led by <strong>Marko Heijnen</strong> (Senior Software Engineer, Jamf) and
            <strong>Javier Casares</strong> (SysAdmin, ROBOTSTXT.es), with mentorship from Lucas Radke.
        </p>

        <h3 class="h5 fw-semibold mt-4 mb-2">Beyond the Hackathon</h3>
        <p>
            WP Plugin Insight does not end with CloudFest 2026. Development continues as an open
            initiative to build trustworthy, code-verified plugin intelligence for the WordPress
            ecosystem. The architecture is built around independent, composable analysis runners —
            each responsible for one concern — making it straightforward to extend with new checks
            and integrations over time.
        </p>
        <p>
            The source code is available at
            <a href="https://github.com/wp-plugin-insights"
               target="_blank"
               rel="noopener noreferrer">github.com/wp-plugin-insights</a>.
        </p>
    </section>

    <hr class="my-4">

    <!-- ── Methodology ───────────────────────────────────────────────────── -->
    <section class="mb-5" aria-labelledby="about-methodology">
        <h2 class="h5 fw-semibold mb-3" id="about-methodology">
            <?php echo htmlspecialchars($i18n->t('about.methodology_title'), ENT_QUOTES, 'UTF-8'); ?>
        </h2>
        <p><?php echo htmlspecialchars($i18n->t('about.methodology_desc'), ENT_QUOTES, 'UTF-8'); ?></p>

        <table class="table table-bordered">
            <caption class="visually-hidden">
                <?php echo htmlspecialchars($i18n->t('about.methodology_title'), ENT_QUOTES, 'UTF-8'); ?>
            </caption>
            <thead>
                <tr>
                    <th scope="col"><?php echo htmlspecialchars($i18n->t('about.col_grade'), ENT_QUOTES, 'UTF-8'); ?></th>
                    <th scope="col"><?php echo htmlspecialchars($i18n->t('about.col_meaning'), ENT_QUOTES, 'UTF-8'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rows = [
                    'a' => $i18n->t('about.grade_a_meaning'),
                    'b' => $i18n->t('about.grade_b_meaning'),
                    'c' => $i18n->t('about.grade_c_meaning'),
                    'd' => $i18n->t('about.grade_d_meaning'),
                    'f' => $i18n->t('about.grade_f_meaning'),
                ];
                foreach ($rows as $letter => $meaning) :
                    ?>
                <tr>
                    <td>
                        <span class="grade grade-<?php echo htmlspecialchars($letter, ENT_QUOTES, 'UTF-8'); ?>"
                              style="width:1.6rem;height:1.6rem;font-size:.85rem"
                              aria-label="<?php echo htmlspecialchars(strtoupper($letter), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars(strtoupper($letter), ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($meaning, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 class="h5 fw-semibold mt-4 mb-3">
            <?php echo htmlspecialchars($i18n->t('about.dimensions_title'), ENT_QUOTES, 'UTF-8'); ?>
        </h2>
        <ul>
            <li>
                <strong><?php echo htmlspecialchars($i18n->t('about.dim_compat'), ENT_QUOTES, 'UTF-8'); ?></strong>
                — <?php echo htmlspecialchars($i18n->t('about.dim_compat_desc'), ENT_QUOTES, 'UTF-8'); ?>
            </li>
            <li>
                <strong><?php echo htmlspecialchars($i18n->t('about.dim_security'), ENT_QUOTES, 'UTF-8'); ?></strong>
                — <?php echo htmlspecialchars($i18n->t('about.dim_security_desc'), ENT_QUOTES, 'UTF-8'); ?>
            </li>
            <li>
                <em><?php echo htmlspecialchars($i18n->t('about.dim_future'), ENT_QUOTES, 'UTF-8'); ?></em>
            </li>
        </ul>

        <h2 class="h5 fw-semibold mt-4 mb-3">
            <?php echo htmlspecialchars($i18n->t('about.sources_title'), ENT_QUOTES, 'UTF-8'); ?>
        </h2>
        <ul>
            <li>
                <a href="https://api.wordpress.org/plugins/info/1.2/"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="WordPress.org Plugins API (opens in new tab)">WordPress.org Plugins API</a>
            </li>
            <li>WPScan / Patchstack <em>(planned)</em></li>
            <li>NVD — National Vulnerability Database <em>(planned)</em></li>
        </ul>
    </section>

    <hr class="my-4">

    <!-- ── Team ──────────────────────────────────────────────────────────── -->
    <section class="mb-5" aria-labelledby="about-team">
        <h2 class="h4 fw-semibold mb-1" id="about-team">Team</h2>
        <p class="text-body-secondary mb-4">The people behind WP Plugin Insight.</p>

        <!-- Co-Leads -->
        <h3 class="h6 text-uppercase text-body-secondary fw-semibold letter-spacing-1 mb-3">Co-Leads</h3>
        <div class="row g-3 mb-5">

            <!-- Javier Casares -->
            <div class="col-sm-6">
                <div class="card h-100 text-center p-3">
                    <div class="mx-auto mb-3"
                         style="width:80px;height:80px;border-radius:50%;background:#dee2e6;
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.8rem;color:#6c757d;overflow:hidden">
                        <img src="/assets/team/javier-casares.jpg"
                             alt="Javier Casares"
                             style="width:100%;height:100%;object-fit:cover"
                             onerror="this.style.display='none';this.parentElement.textContent='JC'">
                    </div>
                    <h4 class="h6 fw-semibold mb-0">
                        Javier Casares
                        <span aria-label="Spain" title="Spain">&#x1F1EA;&#x1F1F8;</span>
                    </h4>
                    <p class="text-body-secondary small mb-2">SysAdmin &middot; ROBOTSTXT.es</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="https://www.linkedin.com/in/javiercasares/"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Javier Casares on LinkedIn">
                            <i class="bi bi-linkedin" aria-hidden="true"></i> LinkedIn
                        </a>
                        <a href="https://github.com/JavierCasares"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Javier Casares on GitHub">
                            <i class="bi bi-github" aria-hidden="true"></i> GitHub
                        </a>
                    </div>
                </div>
            </div>

            <!-- Marko Heijnen -->
            <div class="col-sm-6">
                <div class="card h-100 text-center p-3">
                    <div class="mx-auto mb-3"
                         style="width:80px;height:80px;border-radius:50%;background:#dee2e6;
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.8rem;color:#6c757d;overflow:hidden">
                        <img src="/assets/team/marko-heijnen.jpg"
                             alt="Marko Heijnen"
                             style="width:100%;height:100%;object-fit:cover"
                             onerror="this.style.display='none';this.parentElement.textContent='MH'">
                    </div>
                    <h4 class="h6 fw-semibold mb-0">
                        Marko Heijnen
                        <span aria-label="Netherlands" title="Netherlands">&#x1F1F3;&#x1F1F1;</span>
                    </h4>
                    <p class="text-body-secondary small mb-2">Senior Software Engineer &middot; Jamf</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="https://www.linkedin.com/in/markoheijnen/"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Marko Heijnen on LinkedIn">
                            <i class="bi bi-linkedin" aria-hidden="true"></i> LinkedIn
                        </a>
                        <a href="https://github.com/markoheijnen"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Marko Heijnen on GitHub">
                            <i class="bi bi-github" aria-hidden="true"></i> GitHub
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hackathon Participants -->
        <h3 class="h6 text-uppercase text-body-secondary fw-semibold letter-spacing-1 mb-3">CloudFest Hackathon 2026</h3>
        <div class="row g-3">

            <!-- Cyrille C. -->
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 text-center p-3">
                    <div class="mx-auto mb-2"
                         style="width:64px;height:64px;border-radius:50%;background:#dee2e6;
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.4rem;color:#6c757d;overflow:hidden">
                        <img src="/assets/team/cyrille-c.jpg"
                             alt="Cyrille C."
                             style="width:100%;height:100%;object-fit:cover"
                             onerror="this.style.display='none';this.parentElement.textContent='CC'">
                    </div>
                    <h4 class="h6 fw-semibold mb-0">
                        Cyrille Coquard
                        <span aria-label="France" title="France">&#x1F1EB;&#x1F1F7;</span>
                    </h4>
                    <div class="d-flex gap-2 justify-content-center mt-2">
                        <a href="https://www.linkedin.com/in/cyrille-coquard-0251/"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Cyrille Coquard on LinkedIn">
                            <i class="bi bi-linkedin" aria-hidden="true"></i>
                        </a>
                        <a href="https://github.com/CrochetFeve0251"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Cyrille Coquard on GitHub">
                            <i class="bi bi-github" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Marko Feldmann -->
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 text-center p-3">
                    <div class="mx-auto mb-2"
                         style="width:64px;height:64px;border-radius:50%;background:#dee2e6;
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.4rem;color:#6c757d;overflow:hidden">
                        <img src="/assets/team/marko-feldmann.jpg"
                             alt="Marko Feldmann"
                             style="width:100%;height:100%;object-fit:cover"
                             onerror="this.style.display='none';this.parentElement.textContent='MF'">
                    </div>
                    <h4 class="h6 fw-semibold mb-0">
                        Marko Feldmann
                        <span aria-label="Germany" title="Germany">&#x1F1E9;&#x1F1EA;</span>
                    </h4>
                    <div class="d-flex gap-2 justify-content-center mt-2">
                        <a href="#"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Marko Feldmann on LinkedIn"
                           title="LinkedIn not available">
                            <i class="bi bi-linkedin" aria-hidden="true"></i>
                        </a>
                        <a href="https://github.com/DerHerrFeldmann"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Marko Feldmann on GitHub">
                            <i class="bi bi-github" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Matthias Pfefferle -->
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 text-center p-3">
                    <div class="mx-auto mb-2"
                         style="width:64px;height:64px;border-radius:50%;background:#dee2e6;
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.4rem;color:#6c757d;overflow:hidden">
                        <img src="/assets/team/matthias-pfefferle.jpg"
                             alt="Matthias Pfefferle"
                             style="width:100%;height:100%;object-fit:cover"
                             onerror="this.style.display='none';this.parentElement.textContent='MP'">
                    </div>
                    <h4 class="h6 fw-semibold mb-0">
                        Matthias Pfefferle
                        <span aria-label="Germany" title="Germany">&#x1F1E9;&#x1F1EA;</span>
                    </h4>
                    <div class="d-flex gap-2 justify-content-center mt-2">
                        <a href="https://www.linkedin.com/in/pfefferle/"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Matthias Pfefferle on LinkedIn">
                            <i class="bi bi-linkedin" aria-hidden="true"></i>
                        </a>
                        <a href="https://github.com/pfefferle"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Matthias Pfefferle on GitHub">
                            <i class="bi bi-github" aria-hidden="true"></i>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Erik Torsner -->
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 text-center p-3">
                    <div class="mx-auto mb-2"
                         style="width:64px;height:64px;border-radius:50%;background:#dee2e6;
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.4rem;color:#6c757d;overflow:hidden">
                        <img src="/assets/team/erik-torsner.jpg"
                             alt="Erik Torsner"
                             style="width:100%;height:100%;object-fit:cover"
                             onerror="this.style.display='none';this.parentElement.textContent='ET'">
                    </div>
                    <h4 class="h6 fw-semibold mb-0">
                        Erik Torsner
                        <span aria-label="Sweden" title="Sweden">&#x1F1F8;&#x1F1EA;</span>
                    </h4>
                    <div class="d-flex gap-2 justify-content-center mt-2">
                        <a href="https://www.linkedin.com/in/eriktorsner/"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Erik Torsner on LinkedIn">
                            <i class="bi bi-linkedin" aria-hidden="true"></i>
                        </a>
                        <a href="https://github.com/eriktorsner"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Erik Torsner on GitHub">
                            <i class="bi bi-github" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ralf Wiechers -->
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 text-center p-3">
                    <div class="mx-auto mb-2"
                         style="width:64px;height:64px;border-radius:50%;background:#dee2e6;
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.4rem;color:#6c757d;overflow:hidden">
                        <img src="/assets/team/ralf-wiechers.jpg"
                             alt="Ralf Wiechers"
                             style="width:100%;height:100%;object-fit:cover"
                             onerror="this.style.display='none';this.parentElement.textContent='RW'">
                    </div>
                    <h4 class="h6 fw-semibold mb-0">
                        Ralf Wiechers
                        <span aria-label="Germany" title="Germany">&#x1F1E9;&#x1F1EA;</span>
                    </h4>
                    <div class="d-flex gap-2 justify-content-center mt-2">
                        <a href="https://www.linkedin.com/in/ralf-wiechers/"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Ralf Wiechers on LinkedIn">
                            <i class="bi bi-linkedin" aria-hidden="true"></i>
                        </a>
                        <a href="https://github.com/Drivingralle"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-secondary btn-sm"
                           aria-label="Ralf Wiechers on GitHub">
                            <i class="bi bi-github" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <hr class="my-4">

    <!-- ── Contributors ──────────────────────────────────────────────────── -->
    <section class="mb-5" aria-labelledby="about-contributors">
        <h2 class="h4 fw-semibold mb-2" id="about-contributors">Contributors</h2>
        <p>
            WP Plugin Insight is an open project. See everyone who has contributed on GitHub:
        </p>
        <a href="https://github.com/orgs/wp-plugin-insights/people"
           target="_blank"
           rel="noopener noreferrer"
           class="btn btn-outline-primary">
            <i class="bi bi-github" aria-hidden="true"></i>
            View all contributors on GitHub
        </a>
    </section>

    <hr class="my-4">

    <p class="text-body-secondary small mt-4">
        <?php echo htmlspecialchars($i18n->t('about.disclaimer'), ENT_QUOTES, 'UTF-8'); ?>
    </p>
</main>
