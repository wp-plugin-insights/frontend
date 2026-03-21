<?php

/**
 * Admin panel template.
 *
 * Expected variables (set by index.php):
 *   $i18n                I18n
 *   $settings            array<string, string>      — all site_setting rows
 *   $runners             list<array<string, mixed>> — all runner rows
 *   $exchanges           list<array<string, mixed>> — RabbitMQ exchanges (may be empty)
 *   $queues              list<array<string, mixed>> — RabbitMQ queues (may be empty)
 *   $runnerStats         list<array<string, mixed>> — per-runner result counts + latest date
 *   $recentResults       list<array<string, mixed>> — 20 most recent pluginresult rows
 *   $recentUploads       list<array<string, mixed>> — recent plugin_upload rows
 *   $uploadGrades        array<string, string>      — "{slug}:{version}" → grade letter
 *   $platformStats       array{plugin_count: int, version_count: int}
 *   $analysisStats       array{analyzed_plugins: int, total_results: int}
 *   $pluginSearchTerm    string
 *   $pluginSearchResults list<array<string, mixed>>
 *   $userSearchTerm      string
 *   $userSearchResults   list<array<string, mixed>>
 *   $adminSuccess        string|null
 *   $adminError          string|null
 *
 * esc() is defined as a global helper in index.php.
 */

declare(strict_types=1);

$apiActive   = ($settings['api_active']   ?? '1') === '1';
$apiHostname = $settings['api_hostname']  ?? 'api.plugininsight.com';

$successMessages = [
    'api_settings'  => 'API settings saved.',
    'runner_toggle' => 'Runner status updated.',
    'runner_add'    => 'Runner created.',
    'runner_delete' => 'Runner deleted.',
    'user_admin'    => 'User admin status updated.',
];
$successMsg = isset($adminSuccess, $successMessages[$adminSuccess])
    ? $successMessages[$adminSuccess]
    : null;

// Determine which tab to show on load
$activeTab = 'overview';
if (isset($_GET['tab']) && in_array($_GET['tab'], ['overview','pipeline','plugins','settings'], true)) {
    $activeTab = (string) $_GET['tab'];
} elseif ($adminError !== null) {
    $activeTab = 'pipeline';
} elseif ($userSearchTerm !== '') {
    $activeTab = 'settings';
} elseif ($pluginSearchTerm !== '') {
    $activeTab = 'plugins';
}
?>

<div class="container mt-4 pb-5" style="max-width:920px">

    <h1 class="h3 fw-bold mb-3">
        <i class="bi bi-shield-lock me-2" aria-hidden="true"></i>Admin Panel
    </h1>

    <?php if ($successMsg !== null) : ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?= esc($successMsg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if ($adminError !== null) : ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <?= esc($adminError) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- ── Tab navigation ────────────────────────────────────────────────── -->
    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'overview'  ? 'active' : '' ?>"
                    id="tab-overview-btn"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-overview"
                    type="button"
                    role="tab"
                    aria-controls="tab-overview"
                    aria-selected="<?= $activeTab === 'overview'  ? 'true' : 'false' ?>">
                <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'pipeline'  ? 'active' : '' ?>"
                    id="tab-pipeline-btn"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-pipeline"
                    type="button"
                    role="tab"
                    aria-controls="tab-pipeline"
                    aria-selected="<?= $activeTab === 'pipeline'  ? 'true' : 'false' ?>">
                <i class="bi bi-cpu me-1" aria-hidden="true"></i>Pipeline
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'plugins'   ? 'active' : '' ?>"
                    id="tab-plugins-btn"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-plugins"
                    type="button"
                    role="tab"
                    aria-controls="tab-plugins"
                    aria-selected="<?= $activeTab === 'plugins'   ? 'true' : 'false' ?>">
                <i class="bi bi-puzzle me-1" aria-hidden="true"></i>Plugins
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'settings'  ? 'active' : '' ?>"
                    id="tab-settings-btn"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-settings"
                    type="button"
                    role="tab"
                    aria-controls="tab-settings"
                    aria-selected="<?= $activeTab === 'settings'  ? 'true' : 'false' ?>">
                <i class="bi bi-gear me-1" aria-hidden="true"></i>Settings
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">

    <!-- ══════════════════════════════════════════════════════════════════════
         TAB: Overview
         ═══════════════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade <?= $activeTab === 'overview' ? 'show active' : '' ?>"
         id="tab-overview"
         role="tabpanel"
         aria-labelledby="tab-overview-btn">

        <!-- Platform Statistics -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-bar-chart-line me-1" aria-hidden="true"></i>Platform Statistics
            </div>
            <div class="card-body">
                <div class="row row-cols-2 row-cols-sm-4 g-3 text-center">
                    <div class="col">
                        <div class="fw-bold fs-3"><?= number_format($platformStats['plugin_count']) ?></div>
                        <div class="small text-body-secondary">Plugins tracked</div>
                    </div>
                    <div class="col">
                        <div class="fw-bold fs-3"><?= number_format($platformStats['version_count']) ?></div>
                        <div class="small text-body-secondary">Versions tracked</div>
                    </div>
                    <div class="col">
                        <div class="fw-bold fs-3"><?= number_format($analysisStats['analyzed_plugins']) ?></div>
                        <div class="small text-body-secondary">Plugins analysed</div>
                    </div>
                    <div class="col">
                        <div class="fw-bold fs-3"><?= number_format($analysisStats['total_results']) ?></div>
                        <div class="small text-body-secondary">Analysis results</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Pipeline -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-graph-up me-1" aria-hidden="true"></i>Analysis Pipeline
                <span class="badge text-bg-secondary ms-1">read-only</span>
            </div>
            <div class="card-body p-0">

                <?php
                $queueByName = [];
                foreach ($queues as $q) {
                    $queueByName[(string) ($q['name'] ?? '')] = $q;
                }
                $runnerStatsBySlug = [];
                foreach ($runnerStats as $rs) {
                    $runnerStatsBySlug[(string) $rs['runner_slug']] = $rs;
                }
                ?>

                <?php if (empty($runners)) : ?>
                <p class="text-body-secondary px-3 py-3 mb-0">No runners defined.</p>
                <?php else : ?>
                <div class="table-responsive border-bottom">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Runner</th>
                                <th>Queue</th>
                                <th class="text-end">Results stored</th>
                                <th class="text-end">Ready msgs</th>
                                <th class="text-end">Consumers</th>
                                <th>Last result</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($runners as $runner) : ?>
                            <?php
                            $rSlug      = (string) $runner['runner_slug'];
                            $rQueue     = (string) $runner['runner_queue'];
                            $rActive    = (int) $runner['runner_is_active'] === 1;
                            $rStats     = $runnerStatsBySlug[$rSlug] ?? null;
                            $rQueueData = $queueByName[$rQueue]      ?? null;
                            $rCount     = $rStats     !== null ? (int) $rStats['result_count']           : 0;
                            $rLatest    = $rStats     !== null ? substr((string) $rStats['latest_date'], 0, 16) : '—';
                            $rReady     = $rQueueData !== null ? (int) $rQueueData['messages_ready']     : null;
                            $rConsumers = $rQueueData !== null ? (int) $rQueueData['consumers']          : null;
                            ?>
                            <tr>
                                <td>
                                    <span class="fw-semibold"><?= esc((string) $runner['runner_name']) ?></span>
                                    <br><small class="text-body-secondary plugin-slug"><?= esc($rSlug) ?></small>
                                </td>
                                <td><code class="small"><?= esc($rQueue) ?></code></td>
                                <td class="text-end">
                                    <?php if ($rCount > 0) : ?>
                                    <span class="badge text-bg-light border text-body"><?= number_format($rCount) ?></span>
                                    <?php else : ?>
                                    <span class="text-body-secondary">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($rReady === null) : ?>
                                    <span class="text-body-secondary small">—</span>
                                    <?php elseif ($rReady > 0) : ?>
                                    <span class="badge text-bg-warning"><?= $rReady ?></span>
                                    <?php else : ?>
                                    <span class="text-body-secondary">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($rConsumers === null) : ?>
                                    <span class="text-body-secondary small">—</span>
                                    <?php elseif ($rConsumers > 0) : ?>
                                    <span class="badge text-bg-success"><?= $rConsumers ?></span>
                                    <?php else : ?>
                                    <span class="badge text-bg-secondary">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-body-secondary small"><?= esc($rLatest) ?></td>
                                <td>
                                    <span class="badge <?= $rActive ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                        <?= $rActive ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <div class="px-3 pt-3 pb-1">
                    <h6 class="text-body-secondary mb-2 small text-uppercase fw-semibold">Recent results</h6>
                </div>
                <?php if (empty($recentResults)) : ?>
                <p class="text-body-secondary px-3 pb-3 mb-0 small">No analysis results yet.</p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Plugin</th>
                                <th>Version</th>
                                <th>Runner</th>
                                <th>Grade</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentResults as $res) : ?>
                            <?php
                            $resGrade = strtolower((string) ($res['grade']       ?? ''));
                            $resSlug  = (string) ($res['plugin_slug']  ?? '');
                            ?>
                            <tr>
                                <td>
                                    <?php if ($resSlug !== '') : ?>
                                    <a href="/plugin/<?= esc($resSlug) ?>/"
                                       class="text-decoration-none plugin-slug">
                                        <?= esc($resSlug) ?>
                                    </a>
                                    <?php else : ?>
                                    <span class="text-body-secondary">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-body-secondary small"><?= esc((string) ($res['plugin_version'] ?? '—')) ?></td>
                                <td>
                                    <span class="badge text-bg-light border text-body small">
                                        <?= esc((string) ($res['runner_slug'] ?? '')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($resGrade !== '' && in_array($resGrade, ['a','b','c','d','f'], true)) : ?>
                                    <span class="grade grade-<?= $resGrade ?>"
                                          style="width:1.5rem;height:1.5rem;font-size:.8rem">
                                        <?= strtoupper($resGrade) ?>
                                    </span>
                                    <?php else : ?>
                                    <span class="text-body-secondary small"><?= esc($resGrade !== '' ? $resGrade : '—') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-body-secondary small">
                                    <?= esc(substr((string) ($res['pluginresult_date'] ?? ''), 0, 16)) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div><!-- /tab-overview -->


    <!-- ══════════════════════════════════════════════════════════════════════
         TAB: Pipeline
         ═══════════════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade <?= $activeTab === 'pipeline' ? 'show active' : '' ?>"
         id="tab-pipeline"
         role="tabpanel"
         aria-labelledby="tab-pipeline-btn">

        <!-- Analysis Runners -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-cpu me-1" aria-hidden="true"></i>Analysis Runners
            </div>
            <div class="card-body p-0">
                <?php if (empty($runners)) : ?>
                <p class="text-body-secondary px-3 py-3 mb-0">No runners defined yet.</p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Queue</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($runners as $runner) : ?>
                            <?php
                            $rId     = (int) $runner['runner_id'];
                            $rActive = (int) $runner['runner_is_active'] === 1;
                            ?>
                            <tr>
                                <td><?= esc((string) $runner['runner_name']) ?></td>
                                <td><code><?= esc((string) $runner['runner_slug']) ?></code></td>
                                <td><code><?= esc((string) $runner['runner_queue']) ?></code></td>
                                <td>
                                    <span class="badge <?= $rActive ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                        <?= $rActive ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="post" action="/admin/" class="d-inline">
                                        <?= \PluginInsight\Csrf::field() ?>
                                        <input type="hidden" name="action" value="runner_toggle">
                                        <input type="hidden" name="runner_id" value="<?= $rId ?>">
                                        <input type="hidden" name="runner_is_active" value="<?= $rActive ? 0 : 1 ?>">
                                        <button type="submit"
                                                class="btn btn-sm <?= $rActive ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                                            <?= $rActive ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                    <form method="post" action="/admin/" class="d-inline ms-1"
                                          onsubmit="return confirm('Delete runner <?= esc((string) $runner['runner_name']) ?>?')">
                                        <?= \PluginInsight\Csrf::field() ?>
                                        <input type="hidden" name="action" value="runner_delete">
                                        <input type="hidden" name="runner_id" value="<?= $rId ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <details>
                    <summary class="text-body-secondary small" style="cursor:pointer">Add runner</summary>
                    <form method="post" action="/admin/" class="mt-3 row g-2 align-items-end">
                        <?= \PluginInsight\Csrf::field() ?>
                        <input type="hidden" name="action" value="runner_add">
                        <div class="col-sm-4">
                            <label class="form-label small" for="runner_name">Name</label>
                            <input type="text" id="runner_name" name="runner_name"
                                   class="form-control form-control-sm" maxlength="100" required
                                   placeholder="e.g. AI Analysis">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label small" for="runner_slug">Slug (unique)</label>
                            <input type="text" id="runner_slug" name="runner_slug"
                                   class="form-control form-control-sm" maxlength="50" required
                                   pattern="[a-z0-9_\-]+" placeholder="e.g. ai">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small" for="runner_queue">RabbitMQ Queue</label>
                            <input type="text" id="runner_queue" name="runner_queue"
                                   class="form-control form-control-sm" maxlength="250" required
                                   placeholder="e.g. plugin.analysis.ai">
                        </div>
                        <div class="col-sm-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                        </div>
                    </form>
                </details>
            </div>
        </div>

        <!-- RabbitMQ Queues -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-collection me-1" aria-hidden="true"></i>RabbitMQ Queues
                <span class="badge text-bg-secondary ms-1">read-only</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($queues)) : ?>
                <p class="text-body-secondary px-3 py-3 mb-0">
                    No queue data available. Make sure the RabbitMQ Management plugin is enabled
                    and <code>crons/rabbitmq.php</code> is configured.
                </p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th class="text-end">Ready</th>
                                <th class="text-end">Unacked</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Consumers</th>
                                <th>Durable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($queues as $q) : ?>
                            <?php
                            $qMsgs      = (int) ($q['messages']                ?? 0);
                            $qReady     = (int) ($q['messages_ready']          ?? 0);
                            $qUnacked   = (int) ($q['messages_unacknowledged'] ?? 0);
                            $qConsumers = (int) ($q['consumers']               ?? 0);
                            ?>
                            <tr>
                                <td><code><?= esc((string) ($q['name'] ?? '')) ?></code></td>
                                <td class="text-end">
                                    <?php if ($qReady > 0) : ?>
                                    <span class="badge text-bg-warning"><?= $qReady ?></span>
                                    <?php else : ?>
                                    <span class="text-body-secondary">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($qUnacked > 0) : ?>
                                    <span class="badge text-bg-danger"><?= $qUnacked ?></span>
                                    <?php else : ?>
                                    <span class="text-body-secondary">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?= $qMsgs ?></td>
                                <td class="text-end">
                                    <?php if ($qConsumers > 0) : ?>
                                    <span class="badge text-bg-success"><?= $qConsumers ?></span>
                                    <?php else : ?>
                                    <span class="badge text-bg-secondary">0</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= !empty($q['durable']) ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-body-secondary"></i>' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RabbitMQ Exchanges -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-diagram-3 me-1" aria-hidden="true"></i>RabbitMQ Exchanges
                <span class="badge text-bg-secondary ms-1">read-only</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($exchanges)) : ?>
                <p class="text-body-secondary px-3 py-3 mb-0">
                    No exchange data available. Make sure the RabbitMQ Management plugin is enabled
                    and <code>crons/rabbitmq.php</code> is configured.
                </p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Durable</th>
                                <th>Auto-delete</th>
                                <th>Internal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exchanges as $ex) : ?>
                            <tr>
                                <td>
                                    <?php if ((string) ($ex['name'] ?? '') === '') : ?>
                                    <em class="text-body-secondary">(default)</em>
                                    <?php else : ?>
                                    <code><?= esc((string) ($ex['name'] ?? '')) ?></code>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge text-bg-light border"><?= esc((string) ($ex['type'] ?? '')) ?></span></td>
                                <td><?= !empty($ex['durable'])     ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-body-secondary"></i>' ?></td>
                                <td><?= !empty($ex['auto_delete']) ? '<i class="bi bi-check-circle-fill text-warning"></i>' : '<i class="bi bi-dash text-body-secondary"></i>' ?></td>
                                <td><?= !empty($ex['internal'])    ? '<i class="bi bi-check-circle-fill text-secondary"></i>' : '<i class="bi bi-dash text-body-secondary"></i>' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /tab-pipeline -->


    <!-- ══════════════════════════════════════════════════════════════════════
         TAB: Plugins
         ═══════════════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade <?= $activeTab === 'plugins' ? 'show active' : '' ?>"
         id="tab-plugins"
         role="tabpanel"
         aria-labelledby="tab-plugins-btn">

        <!-- Recent API Uploads -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-cloud-upload me-1" aria-hidden="true"></i>Recent API Uploads
                <span class="badge text-bg-secondary ms-1">read-only</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentUploads)) : ?>
                <p class="text-body-secondary px-3 py-3 mb-0">No uploads yet.</p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Plugin</th>
                                <th>Version</th>
                                <th>Status</th>
                                <th>Grade</th>
                                <th>IP</th>
                                <th>Uploaded</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUploads as $up) : ?>
                            <?php
                            $upStatus  = (string) ($up['upload_status'] ?? 'pending');
                            $upBadge   = match ($upStatus) {
                                'queued' => 'text-bg-primary',
                                'done'   => 'text-bg-success',
                                'error'  => 'text-bg-danger',
                                default  => 'text-bg-secondary',
                            };
                            $upName    = (string) ($up['plugin_name']    ?? $up['plugin_slug'] ?? '—');
                            $upUuid    = (string) ($up['upload_uuid']    ?? '');
                            $upSlug    = (string) ($up['plugin_slug']    ?? '');
                            $upVersion = (string) ($up['plugin_version'] ?? '');
                            $upGrade   = strtolower($uploadGrades[$upSlug . ':' . $upVersion] ?? '');
                            ?>
                            <tr>
                                <td>
                                    <?= esc($upName) ?>
                                    <?php if ($upSlug !== '') : ?>
                                    <br><small class="text-body-secondary plugin-slug"><?= esc($upSlug) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-body-secondary small"><?= esc($upVersion !== '' ? $upVersion : '—') ?></td>
                                <td>
                                    <span class="badge <?= esc($upBadge) ?>"><?= esc($upStatus) ?></span>
                                    <?php if ($upStatus === 'error' && !empty($up['upload_error'])) : ?>
                                    <br><small class="text-danger"><?= esc(substr((string) $up['upload_error'], 0, 60)) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($upGrade !== '' && in_array($upGrade, ['a','b','c','d','f'], true)) : ?>
                                    <span class="grade grade-<?= $upGrade ?>"
                                          style="width:1.5rem;height:1.5rem;font-size:.8rem">
                                        <?= strtoupper($upGrade) ?>
                                    </span>
                                    <?php else : ?>
                                    <span class="text-body-secondary small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-body-secondary small font-monospace"><?= esc((string) ($up['upload_ip'] ?? '')) ?></td>
                                <td class="text-body-secondary small"><?= esc(substr((string) ($up['uploaded_at'] ?? ''), 0, 16)) ?></td>
                                <td>
                                    <?php if ($upUuid !== '') : ?>
                                    <a href="/api/<?= esc($upUuid) ?>/"
                                       class="btn btn-sm <?= $upStatus === 'done' ? 'btn-primary' : 'btn-outline-secondary' ?>"
                                       target="_blank">
                                        <?= $upStatus === 'done' ? 'View Report' : 'View' ?>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Plugin Browser -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-search me-1" aria-hidden="true"></i>Plugin Browser
            </div>
            <div class="card-body">
                <form method="get" action="/admin/" class="d-flex gap-2 mb-3">
                    <input type="hidden" name="tab" value="plugins">
                    <input type="text"
                           name="plugin_search"
                           class="form-control form-control-sm"
                           placeholder="Search by slug…"
                           value="<?= esc($pluginSearchTerm) ?>"
                           maxlength="200"
                           autofocus>
                    <button type="submit" class="btn btn-sm btn-outline-secondary flex-shrink-0">Search</button>
                </form>

                <?php if ($pluginSearchTerm !== '' && empty($pluginSearchResults)) : ?>
                <p class="text-body-secondary small mb-0">
                    No plugins found matching <em><?= esc($pluginSearchTerm) ?></em>.
                </p>
                <?php endif; ?>

                <?php if (!empty($pluginSearchResults)) : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Slug</th>
                                <th>Name</th>
                                <th>Version</th>
                                <th>Last updated</th>
                                <th class="text-end">Results</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pluginSearchResults as $ps) : ?>
                            <?php
                            $psSlug    = (string) ($ps['plugin_slug']         ?? '');
                            $psName    = (string) ($ps['plugin_name']         ?? '');
                            $psVersion = (string) ($ps['plugin_version']      ?? '—');
                            $psUpdated = substr((string) ($ps['plugin_last_updated'] ?? ''), 0, 10);
                            $psCount   = (int) ($ps['result_count'] ?? 0);
                            ?>
                            <tr>
                                <td><code class="plugin-slug"><?= esc($psSlug) ?></code></td>
                                <td class="text-body-secondary small"><?= esc($psName !== '' ? $psName : '—') ?></td>
                                <td class="text-body-secondary small"><?= esc($psVersion) ?></td>
                                <td class="text-body-secondary small"><?= esc($psUpdated !== '' ? $psUpdated : '—') ?></td>
                                <td class="text-end">
                                    <?php if ($psCount > 0) : ?>
                                    <span class="badge text-bg-success"><?= $psCount ?></span>
                                    <?php else : ?>
                                    <span class="badge text-bg-light border text-body-secondary">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($psSlug !== '') : ?>
                                    <a href="/plugin/<?= esc($psSlug) ?>/"
                                       class="btn btn-sm btn-outline-secondary"
                                       target="_blank">View</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /tab-plugins -->


    <!-- ══════════════════════════════════════════════════════════════════════
         TAB: Settings
         ═══════════════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade <?= $activeTab === 'settings' ? 'show active' : '' ?>"
         id="tab-settings"
         role="tabpanel"
         aria-labelledby="tab-settings-btn">

        <!-- API Settings -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-cloud me-1" aria-hidden="true"></i>API Settings
            </div>
            <div class="card-body">
                <form method="post" action="/admin/">
                    <?= \PluginInsight\Csrf::field() ?>
                    <input type="hidden" name="action" value="api_settings">

                    <div class="mb-3">
                        <label for="api_hostname" class="form-label">API Hostname</label>
                        <input type="text"
                               id="api_hostname"
                               name="api_hostname"
                               class="form-control"
                               value="<?= esc($apiHostname) ?>"
                               placeholder="api.plugininsight.com"
                               maxlength="253"
                               pattern="[a-zA-Z0-9._\-]+"
                               required>
                        <div class="form-text">Used to construct result URLs returned by the upload endpoint.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="api_active"
                                   name="api_active"
                                   role="switch"
                                   <?= $apiActive ? 'checked' : '' ?>>
                            <label class="form-check-label" for="api_active">
                                API active
                                <span class="badge ms-1 <?= $apiActive ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                    <?= $apiActive ? 'ON' : 'OFF' ?>
                                </span>
                            </label>
                        </div>
                        <div class="form-text">
                            When disabled the API returns <code>503 Service Unavailable</code> for all requests.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">Save API settings</button>
                </form>
            </div>
        </div>

        <!-- User Management -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-people me-1" aria-hidden="true"></i>User Management
            </div>
            <div class="card-body">
                <form method="get" action="/admin/" class="d-flex gap-2 mb-3">
                    <input type="hidden" name="tab" value="settings">
                    <input type="text"
                           name="user_search"
                           class="form-control form-control-sm"
                           placeholder="Search by e-mail…"
                           value="<?= esc($userSearchTerm) ?>"
                           maxlength="254">
                    <button type="submit" class="btn btn-sm btn-outline-secondary flex-shrink-0">Search</button>
                </form>

                <?php if ($userSearchTerm !== '' && empty($userSearchResults)) : ?>
                <p class="text-body-secondary small mb-0">
                    No users found matching <em><?= esc($userSearchTerm) ?></em>.
                </p>
                <?php endif; ?>

                <?php if (!empty($userSearchResults)) : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>E-mail</th>
                                <th>Display name</th>
                                <th>Admin</th>
                                <th>Joined</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userSearchResults as $u) : ?>
                            <?php
                            $uId      = (int) $u['user_id'];
                            $uIsAdmin = (int) ($u['user_is_admin'] ?? 0) === 1;
                            $isSelf   = $uId === (int) ($auth?->currentUser()['user_id'] ?? 0);
                            ?>
                            <tr>
                                <td><?= esc((string) ($u['email'] ?? '')) ?></td>
                                <td class="text-body-secondary"><?= esc((string) ($u['display_name'] ?? '—')) ?></td>
                                <td>
                                    <?php if ($uIsAdmin) : ?>
                                    <span class="badge text-bg-warning">Admin</span>
                                    <?php else : ?>
                                    <span class="badge text-bg-light border text-body-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-body-secondary small">
                                    <?= esc(substr((string) ($u['created_at'] ?? ''), 0, 10)) ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($isSelf) : ?>
                                    <span class="text-body-secondary small">You</span>
                                    <?php else : ?>
                                    <form method="post" action="/admin/" class="d-inline">
                                        <?= \PluginInsight\Csrf::field() ?>
                                        <input type="hidden" name="action" value="user_admin">
                                        <input type="hidden" name="user_id" value="<?= $uId ?>">
                                        <input type="hidden" name="user_is_admin" value="<?= $uIsAdmin ? 0 : 1 ?>">
                                        <?php if (!empty($userSearchTerm)) : ?>
                                        <input type="hidden" name="user_search_return" value="<?= esc($userSearchTerm) ?>">
                                        <?php endif; ?>
                                        <button type="submit"
                                                class="btn btn-sm <?= $uIsAdmin ? 'btn-outline-warning' : 'btn-outline-primary' ?>">
                                            <?= $uIsAdmin ? 'Revoke admin' : 'Make admin' ?>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /tab-settings -->

    </div><!-- /.tab-content -->

</div><!-- /.container -->

<script>
(function () {
    'use strict';

    // Update URL hash when switching tabs (for bookmarking)
    var tabButtons = document.querySelectorAll('#adminTabs [data-bs-toggle="tab"]');
    tabButtons.forEach(function (btn) {
        btn.addEventListener('shown.bs.tab', function (e) {
            var target = e.target.getAttribute('data-bs-target');
            if (target) {
                history.replaceState(null, '', '#' + target.replace('#tab-', ''));
            }
        });
    });
}());
</script>
