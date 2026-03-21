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
];
