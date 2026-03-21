<?php

/**
 * Dutch (nl) translations.
 */

declare(strict_types=1);

return [
    // Navigation & chrome
    'nav.home'              => 'Home',
    'nav.about'             => 'Over ons',
    'theme.to_light'        => 'Overschakelen naar lichte modus',
    'theme.to_dark'         => 'Overschakelen naar donkere modus',

    // Footer
    'footer.tagline'        => 'PluginInsight — onafhankelijke kwaliteitssignalen voor WordPress-plugins.',
    'footer.disclaimer'     => 'Niet gelieerd aan WordPress.org of Automattic.',

    // Homepage
    'home.page_title'       => 'WP Plugin Insights — WordPress-pluginkwaliteitsgids',
    'home.meta_desc'        => 'Analyseer de kwaliteit, beveiliging en het onderhoud van elke WordPress.org-plugin. Cijfers in een oogopslag.',
    'home.headline'         => 'WordPress-pluginkwaliteit in één oogopslag',
    'home.subheadline'      => 'Zoek een plugin van WordPress.org en ontvang een helder kwaliteits- en beveiligingsrapport.',
    'home.search_placeholder' => 'Plugin-slug of naam — bijv. woocommerce',
    'home.search_btn'       => 'Analyseren',
    'home.recently_reviewed' => 'Onlangs beoordeeld',
    'home.updated_on'       => 'Bijgewerkt',
    'home.no_plugins'       => 'Nog geen plugins in de database. Kom later terug.',
    'home.db_count'         => '{count} plugins in de database',

    // Plugin detail — header
    'plugin.page_title'     => '{name} — WP Plugin Insights',
    'plugin.meta_desc'      => 'Kwaliteits-, beveiligings- en compatibiliteitsanalyse van de WordPress-plugin {name}.',
    'plugin.wp_org_link'    => 'WordPress.org',
    'plugin.downloads'      => 'downloads',
    'plugin.active_installs' => 'actieve installaties',
    'plugin.version'        => 'Versie',
    'plugin.updated'        => 'Bijgewerkt',
    'plugin.added'          => 'Toegevoegd',
    'plugin.no_data'        => '—',

    // Plugin detail — compatibility card
    'plugin.compat_title'         => 'Compatibiliteit & Vereisten',
    'plugin.compat_requires_wp'   => 'Vereist WordPress',
    'plugin.compat_tested'        => 'Getest tot',
    'plugin.compat_requires_php'  => 'Vereist PHP',
    'plugin.compat_last_updated'  => 'Laatst bijgewerkt',
    'plugin.compat_dependencies'  => 'Plugin-afhankelijkheden',
    'plugin.compat_or_higher'     => 'of hoger',
    'plugin.compat_none'          => 'Geen',
    'plugin.compat_badge_current' => 'Actueel',
    'plugin.compat_badge_recent'  => 'Recent',
    'plugin.compat_badge_outdated' => 'Verouderd',

    // Plugin detail — security card
    'plugin.security_title'       => 'Beveiliging',
    'plugin.security_pending'     => 'Beveiligingsanalyse is nog niet beschikbaar voor deze plugin.',

    // Plugin detail — grade legend
    'plugin.grade_scale'      => 'Cijferschaal',
    'plugin.grade_a'          => 'Uitstekend',
    'plugin.grade_b'          => 'Goed',
    'plugin.grade_c'          => 'Acceptabel',
    'plugin.grade_d'          => 'Slecht',
    'plugin.grade_f'          => 'Onvoldoende',
    'plugin.grade_pending'    => 'In behandeling',

    // Plugin not found
    'plugin.not_found_title'  => 'Plugin niet gevonden',
    'plugin.not_found_heading' => 'Plugin niet gevonden',
    'plugin.not_found_desc'   => 'Geen gegevens gevonden voor plugin <code>{slug}</code>.',
    'plugin.not_found_back'   => 'Terug naar home',

    // About
    'about.page_title'        => 'Over ons — WP Plugin Insights',
    'about.heading'           => 'Over WP Plugin Insights',
    'about.intro'             => 'PluginInsight is een onafhankelijk platform dat WordPress.org-plugins analyseert op meerdere kwaliteitsdimensies en cijfers (A–F) toekent om ontwikkelaars, site-eigenaren en hostingteams een duidelijk en bruikbaar signaal te geven.',
    'about.methodology_title' => 'Beoordelingsmethodiek',
    'about.methodology_desc'  => 'Elke plugin wordt per dimensie beoordeeld. Het eindcijfer weerspiegelt de laagste individuele score, gewogen naar ernst.',
    'about.col_grade'         => 'Cijfer',
    'about.col_meaning'       => 'Betekenis',
    'about.grade_a_meaning'   => 'Uitstekend — geen significante problemen',
    'about.grade_b_meaning'   => 'Goed — alleen kleine problemen',
    'about.grade_c_meaning'   => 'Acceptabel — enkele aandachtspunten',
    'about.grade_d_meaning'   => 'Slecht — significante problemen aanwezig',
    'about.grade_f_meaning'   => 'Onvoldoende — kritieke problemen of verlaten plugin',
    'about.dimensions_title'  => 'Analysedimensies',
    'about.dim_compat'        => 'Compatibiliteit & Vereisten',
    'about.dim_compat_desc'   => 'Compatibiliteit met WordPress- en PHP-versies, datum van laatste update.',
    'about.dim_security'      => 'Beveiliging',
    'about.dim_security_desc' => 'Bekende CVE\'s uit WPScan / Patchstack / NVD, patchstatus.',
    'about.dim_future'        => 'Codekwaliteit, vertalingen, prestaties, onderhoud, licentie — beschikbaar in toekomstige fasen.',
    'about.sources_title'     => 'Gegevensbronnen',
    'about.disclaimer'        => 'PluginInsight is niet gelieerd aan WordPress.org, de WordPress Foundation of Automattic.',

    // 404
    '404.page_title'          => 'Pagina niet gevonden — WP Plugin Insights',
    '404.heading'             => 'Pagina niet gevonden',
    '404.desc'                => 'De pagina die u zocht bestaat niet.',
    '404.back'                => 'Naar de homepage',
    'auth.register_title'             => 'Account aanmaken — WP Plugin Insights',
    'auth.register_heading'           => 'Account aanmaken',
    'auth.register_btn'               => 'Account aanmaken',
    'auth.register_have_account'      => 'Heeft u al een account?',
    'auth.register_login_link'        => 'Inloggen',
    'auth.register_err_email_taken'   => 'Dat e-mailadres is al geregistreerd.',
    'auth.register_err_invalid_email' => 'Voer een geldig e-mailadres in.',
    'auth.register_success'           => 'Account aangemaakt. U kunt nu inloggen.',
    'auth.login_register'             => 'Account aanmaken',
    'auth.login_new_here'             => 'Nieuw hier?',
    'plugin.author' => 'Auteur',
    'plugin.source' => 'Bron',

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
