<?php

/**
 * Italian (it) translations.
 */

declare(strict_types=1);

return [
    // Navigation & chrome
    'nav.home'              => 'Home',
    'nav.about'             => 'Chi siamo',
    'theme.to_light'        => 'Passa alla modalità chiara',
    'theme.to_dark'         => 'Passa alla modalità scura',

    // Footer
    'footer.tagline'        => 'PluginInsight — segnali di qualità indipendenti per i plugin WordPress.',
    'footer.disclaimer'     => 'Non affiliato a WordPress.org né ad Automattic.',

    // Homepage
    'home.page_title'       => 'WP Plugin Insights — Directory della qualità dei plugin WordPress',
    'home.meta_desc'        => 'Analizza qualità, sicurezza e manutenzione di qualsiasi plugin WordPress.org. Voti in lettere a colpo d\'occhio.',
    'home.headline'         => 'Qualità dei plugin WordPress a colpo d\'occhio',
    'home.subheadline'      => 'Cerca qualsiasi plugin da WordPress.org e ottieni un report chiaro su qualità e sicurezza.',
    'home.search_placeholder' => 'Slug o nome del plugin — es. woocommerce',
    'home.search_btn'       => 'Analizza',
    'home.recently_reviewed' => 'Recensiti di recente',
    'home.updated_on'       => 'Aggiornato',
    'home.no_plugins'       => 'Nessun plugin nel database per ora. Torna presto.',
    'home.db_count'         => '{count} plugin nel database',

    // Plugin detail — header
    'plugin.page_title'     => '{name} — WP Plugin Insights',
    'plugin.meta_desc'      => 'Analisi di qualità, sicurezza e compatibilità del plugin WordPress {name}.',
    'plugin.wp_org_link'    => 'WordPress.org',
    'plugin.downloads'      => 'download',
    'plugin.active_installs' => 'installazioni attive',
    'plugin.version'        => 'Versione',
    'plugin.updated'        => 'Aggiornato',
    'plugin.added'          => 'Aggiunto',
    'plugin.no_data'        => '—',

    // Plugin detail — compatibility card
    'plugin.compat_title'         => 'Compatibilità & Requisiti',
    'plugin.compat_requires_wp'   => 'Richiede WordPress',
    'plugin.compat_tested'        => 'Testato fino a',
    'plugin.compat_requires_php'  => 'Richiede PHP',
    'plugin.compat_last_updated'  => 'Ultimo aggiornamento',
    'plugin.compat_dependencies'  => 'Dipendenze da plugin',
    'plugin.compat_or_higher'     => 'o superiore',
    'plugin.compat_none'          => 'Nessuna',
    'plugin.compat_badge_current' => 'Attuale',
    'plugin.compat_badge_recent'  => 'Recente',
    'plugin.compat_badge_outdated' => 'Obsoleto',

    // Plugin detail — security card
    'plugin.security_title'       => 'Sicurezza',
    'plugin.security_pending'     => 'L\'analisi della sicurezza non è ancora disponibile per questo plugin.',

    // Plugin detail — grade legend
    'plugin.grade_scale'      => 'Scala dei voti',
    'plugin.grade_a'          => 'Eccellente',
    'plugin.grade_b'          => 'Buono',
    'plugin.grade_c'          => 'Accettabile',
    'plugin.grade_d'          => 'Scarso',
    'plugin.grade_f'          => 'Insufficiente',
    'plugin.grade_pending'    => 'In attesa',

    // Plugin not found
    'plugin.not_found_title'  => 'Plugin non trovato',
    'plugin.not_found_heading' => 'Plugin non trovato',
    'plugin.not_found_desc'   => 'Nessun dato trovato per il plugin <code>{slug}</code>.',
    'plugin.not_found_back'   => 'Torna alla home',

    // About
    'about.page_title'        => 'Chi siamo — WP Plugin Insights',
    'about.heading'           => 'Chi siamo — WP Plugin Insights',
    'about.intro'             => 'PluginInsight è una piattaforma indipendente che analizza i plugin WordPress.org secondo molteplici dimensioni di qualità e assegna voti in lettere (A–F) per fornire a sviluppatori, proprietari di siti e team di hosting un segnale chiaro e immediatamente fruibile.',
    'about.methodology_title' => 'Metodologia di valutazione',
    'about.methodology_desc'  => 'Ogni plugin viene valutato per dimensione. Il voto complessivo riflette il punteggio individuale più basso, ponderato per gravità.',
    'about.col_grade'         => 'Voto',
    'about.col_meaning'       => 'Significato',
    'about.grade_a_meaning'   => 'Eccellente — nessun problema significativo',
    'about.grade_b_meaning'   => 'Buono — solo problemi minori',
    'about.grade_c_meaning'   => 'Accettabile — alcuni aspetti da esaminare',
    'about.grade_d_meaning'   => 'Scarso — problemi significativi presenti',
    'about.grade_f_meaning'   => 'Insufficiente — problemi critici o plugin abbandonato',
    'about.dimensions_title'  => 'Dimensioni di analisi',
    'about.dim_compat'        => 'Compatibilità & Requisiti',
    'about.dim_compat_desc'   => 'Compatibilità con le versioni di WordPress e PHP, data dell\'ultimo aggiornamento.',
    'about.dim_security'      => 'Sicurezza',
    'about.dim_security_desc' => 'CVE noti da WPScan / Patchstack / NVD, stato delle patch.',
    'about.dim_future'        => 'Qualità del codice, traduzioni, prestazioni, manutenzione, licenza — disponibili nelle fasi future.',
    'about.sources_title'     => 'Fonti dei dati',
    'about.disclaimer'        => 'PluginInsight non è affiliato a WordPress.org, alla WordPress Foundation né ad Automattic.',

    // 404
    '404.page_title'          => 'Pagina non trovata — WP Plugin Insights',
    '404.heading'             => 'Pagina non trovata',
    '404.desc'                => 'La pagina che cercavi non esiste.',
    '404.back'                => 'Vai alla home',
    'auth.register_title'             => 'Crea account — WP Plugin Insights',
    'auth.register_heading'           => 'Crea un account',
    'auth.register_btn'               => 'Crea account',
    'auth.register_have_account'      => 'Hai già un account?',
    'auth.register_login_link'        => 'Accedi',
    'auth.register_err_email_taken'   => 'Quell\'indirizzo e-mail è già registrato.',
    'auth.register_err_invalid_email' => 'Inserisci un indirizzo e-mail valido.',
    'auth.register_success'           => 'Account creato. Ora puoi accedere.',
    'auth.login_register'             => 'Crea account',
    'auth.login_new_here'             => 'Nuovo qui?',
    'plugin.author' => 'Autore',
    'plugin.source' => 'Fonte',

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
