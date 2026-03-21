<?php

/**
 * German (de) translations.
 */

declare(strict_types=1);

return [
    // Navigation & chrome
    'nav.home'              => 'Startseite',
    'nav.about'             => 'Über uns',
    'theme.to_light'        => 'Zum hellen Modus wechseln',
    'theme.to_dark'         => 'Zum dunklen Modus wechseln',

    // Footer
    'footer.tagline'        => 'PluginInsight — unabhängige Qualitätssignale für WordPress-Plugins.',
    'footer.disclaimer'     => 'Nicht verbunden mit WordPress.org oder Automattic.',

    // Homepage
    'home.page_title'       => 'WP Plugin Insights — WordPress-Plugin-Qualitätsverzeichnis',
    'home.meta_desc'        => 'Analysieren Sie Qualität, Sicherheit und Pflege jedes WordPress.org-Plugins. Buchstabennoten auf einen Blick.',
    'home.headline'         => 'WordPress-Plugin-Qualität auf einen Blick',
    'home.subheadline'      => 'Suchen Sie ein beliebiges Plugin von WordPress.org und erhalten Sie einen klaren Qualitäts- und Sicherheitsbericht.',
    'home.search_placeholder' => 'Plugin-Slug oder Name — z. B. woocommerce',
    'home.search_btn'       => 'Analysieren',
    'home.recently_reviewed' => 'Zuletzt geprüft',
    'home.updated_on'       => 'Aktualisiert',
    'home.no_plugins'       => 'Noch keine Plugins in der Datenbank. Schauen Sie bald wieder vorbei.',
    'home.db_count'         => '{count} Plugins in der Datenbank',

    // Plugin detail — header
    'plugin.page_title'     => '{name} — WP Plugin Insights',
    'plugin.meta_desc'      => 'Qualitäts-, Sicherheits- und Kompatibilitätsanalyse für das WordPress-Plugin {name}.',
    'plugin.wp_org_link'    => 'WordPress.org',
    'plugin.downloads'      => 'Downloads',
    'plugin.active_installs' => 'aktive Installationen',
    'plugin.version'        => 'Version',
    'plugin.updated'        => 'Aktualisiert',
    'plugin.added'          => 'Hinzugefügt',
    'plugin.no_data'        => '—',

    // Plugin detail — compatibility card
    'plugin.compat_title'         => 'Kompatibilität & Anforderungen',
    'plugin.compat_requires_wp'   => 'Erfordert WordPress',
    'plugin.compat_tested'        => 'Getestet bis',
    'plugin.compat_requires_php'  => 'Erfordert PHP',
    'plugin.compat_last_updated'  => 'Zuletzt aktualisiert',
    'plugin.compat_dependencies'  => 'Plugin-Abhängigkeiten',
    'plugin.compat_or_higher'     => 'oder höher',
    'plugin.compat_none'          => 'Keine',
    'plugin.compat_badge_current' => 'Aktuell',
    'plugin.compat_badge_recent'  => 'Neulich',
    'plugin.compat_badge_outdated' => 'Veraltet',

    // Plugin detail — security card
    'plugin.security_title'       => 'Sicherheit',
    'plugin.security_pending'     => 'Sicherheitsanalyse für dieses Plugin noch nicht verfügbar.',

    // Plugin detail — grade legend
    'plugin.grade_scale'      => 'Notenskala',
    'plugin.grade_a'          => 'Ausgezeichnet',
    'plugin.grade_b'          => 'Gut',
    'plugin.grade_c'          => 'Akzeptabel',
    'plugin.grade_d'          => 'Mangelhaft',
    'plugin.grade_f'          => 'Ungenügend',
    'plugin.grade_pending'    => 'Ausstehend',

    // Plugin not found
    'plugin.not_found_title'  => 'Plugin nicht gefunden',
    'plugin.not_found_heading' => 'Plugin nicht gefunden',
    'plugin.not_found_desc'   => 'Keine Daten für das Plugin <code>{slug}</code> gefunden.',
    'plugin.not_found_back'   => 'Zurück zur Startseite',

    // About
    'about.page_title'        => 'Über uns — WP Plugin Insights',
    'about.heading'           => 'Über WP Plugin Insights',
    'about.intro'             => 'PluginInsight ist eine unabhängige Plattform, die WordPress.org-Plugins anhand mehrerer Qualitätsdimensionen analysiert und Buchstabennoten (A–F) vergibt, um Entwicklern, Website-Betreibern und Hosting-Teams ein klares, umsetzbares Signal zu geben.',
    'about.methodology_title' => 'Bewertungsmethodik',
    'about.methodology_desc'  => 'Jedes Plugin wird pro Dimension bewertet. Die Gesamtnote entspricht der niedrigsten Einzelnote, gewichtet nach Schweregrad.',
    'about.col_grade'         => 'Note',
    'about.col_meaning'       => 'Bedeutung',
    'about.grade_a_meaning'   => 'Ausgezeichnet — keine wesentlichen Probleme',
    'about.grade_b_meaning'   => 'Gut — nur kleinere Probleme',
    'about.grade_c_meaning'   => 'Akzeptabel — einige Punkte zur Überprüfung',
    'about.grade_d_meaning'   => 'Mangelhaft — erhebliche Probleme vorhanden',
    'about.grade_f_meaning'   => 'Ungenügend — kritische Probleme oder aufgegebenes Plugin',
    'about.dimensions_title'  => 'Analysedimensionen',
    'about.dim_compat'        => 'Kompatibilität & Anforderungen',
    'about.dim_compat_desc'   => 'Kompatibilität mit WordPress- und PHP-Versionen, Datum der letzten Aktualisierung.',
    'about.dim_security'      => 'Sicherheit',
    'about.dim_security_desc' => 'Bekannte CVEs aus WPScan / Patchstack / NVD, Patch-Status.',
    'about.dim_future'        => 'Codequalität, Übersetzungen, Performance, Wartung, Lizenz — in zukünftigen Phasen verfügbar.',
    'about.sources_title'     => 'Datenquellen',
    'about.disclaimer'        => 'PluginInsight ist nicht mit WordPress.org, der WordPress Foundation oder Automattic verbunden.',

    // 404
    '404.page_title'          => 'Seite nicht gefunden — WP Plugin Insights',
    '404.heading'             => 'Seite nicht gefunden',
    '404.desc'                => 'Die gesuchte Seite existiert nicht.',
    '404.back'                => 'Zur Startseite',
    'auth.register_title'             => 'Konto erstellen — WP Plugin Insights',
    'auth.register_heading'           => 'Konto erstellen',
    'auth.register_btn'               => 'Konto erstellen',
    'auth.register_have_account'      => 'Haben Sie bereits ein Konto?',
    'auth.register_login_link'        => 'Anmelden',
    'auth.register_err_email_taken'   => 'Diese E-Mail-Adresse ist bereits registriert.',
    'auth.register_err_invalid_email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
    'auth.register_success'           => 'Konto erstellt. Sie können sich jetzt anmelden.',
    'auth.login_register'             => 'Konto erstellen',
    'auth.login_new_here'             => 'Neu hier?',
    'plugin.author' => 'Autor',
    'plugin.source' => 'Quelle',

    // Plugin detail — WP–PHP compatibility row
    'plugin.wp_php_compat'      => 'WP–PHP compatibility',
    'plugin.wp_php_compat_info' => 'WP {wp}+ requires PHP {php}+. This plugin does not declare a minimum PHP version.',
    'plugin.wp_php_compat_warn' => 'WP {wp}+ requires PHP {php}+, but this plugin only declares PHP {declared}+. The declared PHP minimum may be too low.',
    'plugin.wp_php_compat_ok'   => 'PHP {declared}+ is compatible with WP {wp}+ (which requires PHP {php}+).',

    // Runner cards — PHP compatibility block
    'runner.php_declared'           => 'Declared PHP',
    'runner.php_detected'           => 'Detected PHP',
    'runner.php_tested_versions'    => 'Tested versions',
    'runner.php_effective_min'      => 'Effective minimum: PHP {version}+.',
    'runner.php_grade_note_match'   => 'Declared PHP {declared}+ matches the effective minimum ({effective}+)',
    'runner.php_grade_note_over'    => 'Declared PHP {declared}+ is above the effective minimum ({effective}+) — unnecessarily strict',
    'runner.php_grade_note_under'   => 'Declared PHP {declared}+ is below the effective minimum ({effective}+) — incorrect declaration',

    // Runner cards — wp-since block
    'runner.wp_declared_min'        => 'Declared min WP',
    'runner.wp_suggested_min'       => 'Suggested min WP',
    'runner.tool_output'            => 'Tool output',

    // Runner cards — translate block
    'runner.locales_detected'       => 'Locales detected',
    'runner.locales_compliant'      => 'Compliant (≥80%)',
    'runner.untranslated_strings'   => 'Untranslated strings',
    'runner.text_domain'            => 'Text domain',
    'runner.load_textdomain'        => 'load_plugin_textdomain',
    'runner.js_strings_translated'  => 'JS strings translated',
    'runner.supported_locales'      => 'Supported locales',
    'runner.no_locale_coverage'     => 'No locale reaches 80% coverage.',
    'runner.coverage_by_locale'     => 'Coverage by locale',
    'runner.locale_col'             => 'Locale',
    'runner.language_col'           => 'Language',
    'runner.coverage_col'           => 'Coverage',
    'runner.td_declared'            => 'Declared',
    'runner.td_expected'            => 'Expected',
    'runner.td_usage'               => 'Usage found',
    'runner.td_valid'               => 'valid',
    'runner.td_invalid'             => 'invalid',
    'runner.untranslated_preview'   => 'Untranslated strings (first {count})',
    'runner.str_col'                => 'String',
    'runner.file_col'               => 'File',
    'runner.line_col'               => 'Line',
    'runner.severity_high'          => 'high',
    'runner.severity_medium'        => 'medium',
    'runner.severity_low'           => 'low',
    'runner.severity_trivial'       => 'trivial',

    // Runner cards — translate grade adjustment notes
    'runner.tr_note_no_textdomain'      => 'No text domain is declared — grade penalised −2',
    'runner.tr_note_invalid_textdomain' => 'Text domain is invalid or mismatched — grade penalised −1',
    'runner.tr_note_high'               => '{count} high-severity issue(s) — grade penalised −1',
    'runner.tr_note_many_high'          => '{count} high-severity issues — grade penalised −2',
    'runner.tr_note_many_medium'        => '{count} medium-severity issues — grade penalised −1',

    // Plugin detail — Compatibility & Requirements grade notes
    'plugin.compat_note_no_wp'           => 'No minimum WordPress version is declared',
    'plugin.compat_note_no_php'          => 'No minimum PHP version is declared',
    'plugin.compat_note_php_wp_mismatch' => 'Declared PHP minimum is below what the target WordPress version requires',
    'plugin.compat_note_outdated'        => 'Plugin has not been updated in over a year',
    'plugin.compat_note_tested_outdated' => 'Plugin has not been tested with the current WordPress version',
];
