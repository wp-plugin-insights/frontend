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
];
