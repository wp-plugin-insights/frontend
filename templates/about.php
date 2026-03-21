<?php

/**
 * About page template.
 *
 * Expected variables (set by index.php before including this file):
 *   $i18n I18n
 */

declare(strict_types=1);

?>

<main class="container py-5" style="max-width:760px">
    <h1 class="fw-bold mb-4"><?= htmlspecialchars($i18n->t('about.heading'), ENT_QUOTES, 'UTF-8') ?></h1>

    <p class="lead"><?= htmlspecialchars($i18n->t('about.intro'), ENT_QUOTES, 'UTF-8') ?></p>

    <hr class="my-4">

    <h2 class="h5 fw-semibold mb-3"><?= htmlspecialchars($i18n->t('about.methodology_title'), ENT_QUOTES, 'UTF-8') ?></h2>
    <p><?= htmlspecialchars($i18n->t('about.methodology_desc'), ENT_QUOTES, 'UTF-8') ?></p>

    <table class="table table-bordered">
        <caption class="visually-hidden"><?= htmlspecialchars($i18n->t('about.methodology_title'), ENT_QUOTES, 'UTF-8') ?></caption>
        <thead>
            <tr>
                <th scope="col"><?= htmlspecialchars($i18n->t('about.col_grade'), ENT_QUOTES, 'UTF-8') ?></th>
                <th scope="col"><?= htmlspecialchars($i18n->t('about.col_meaning'), ENT_QUOTES, 'UTF-8') ?></th>
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
                    <span class="grade grade-<?= htmlspecialchars($letter, ENT_QUOTES, 'UTF-8') ?>"
                          style="width:1.6rem;height:1.6rem;font-size:.85rem"
                          aria-label="<?= htmlspecialchars(strtoupper($letter), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars(strtoupper($letter), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($meaning, ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="h5 fw-semibold mt-4 mb-3"><?= htmlspecialchars($i18n->t('about.dimensions_title'), ENT_QUOTES, 'UTF-8') ?></h2>
    <ul>
        <li>
            <strong><?= htmlspecialchars($i18n->t('about.dim_compat'), ENT_QUOTES, 'UTF-8') ?></strong>
            — <?= htmlspecialchars($i18n->t('about.dim_compat_desc'), ENT_QUOTES, 'UTF-8') ?>
        </li>
        <li>
            <strong><?= htmlspecialchars($i18n->t('about.dim_security'), ENT_QUOTES, 'UTF-8') ?></strong>
            — <?= htmlspecialchars($i18n->t('about.dim_security_desc'), ENT_QUOTES, 'UTF-8') ?>
        </li>
        <li>
            <em><?= htmlspecialchars($i18n->t('about.dim_future'), ENT_QUOTES, 'UTF-8') ?></em>
        </li>
    </ul>

    <h2 class="h5 fw-semibold mt-4 mb-3"><?= htmlspecialchars($i18n->t('about.sources_title'), ENT_QUOTES, 'UTF-8') ?></h2>
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

    <p class="text-body-secondary small mt-4"><?= htmlspecialchars($i18n->t('about.disclaimer'), ENT_QUOTES, 'UTF-8') ?></p>
</main>
