<?php

/**
 * French (fr) translations.
 */

declare(strict_types=1);

return [
    // Navigation & chrome
    'nav.home'              => 'Accueil',
    'nav.about'             => 'À propos',
    'theme.to_light'        => 'Passer en mode clair',
    'theme.to_dark'         => 'Passer en mode sombre',

    // Footer
    'footer.tagline'        => 'PluginInsight — signaux de qualité indépendants pour les plugins WordPress.',
    'footer.disclaimer'     => 'Non affilié à WordPress.org ni à Automattic.',

    // Homepage
    'home.page_title'       => 'WP Plugin Insights — Répertoire de qualité des plugins WordPress',
    'home.meta_desc'        => 'Analysez la qualité, la sécurité et la maintenance de n\'importe quel plugin WordPress.org. Notes en lettres en un coup d\'œil.',
    'home.headline'         => 'Qualité des plugins WordPress en un coup d\'œil',
    'home.subheadline'      => 'Recherchez n\'importe quel plugin WordPress.org et obtenez un rapport clair sur sa qualité et sa sécurité.',
    'home.search_placeholder' => 'Slug ou nom du plugin — ex. woocommerce',
    'home.search_btn'       => 'Analyser',
    'home.recently_reviewed' => 'Récemment examinés',
    'home.updated_on'       => 'Mis à jour',
    'home.no_plugins'       => 'Aucun plugin dans la base de données pour l\'instant. Revenez bientôt.',
    'home.db_count'         => '{count} plugins dans la base de données',

    // Plugin detail — header
    'plugin.page_title'     => '{name} — WP Plugin Insights',
    'plugin.meta_desc'      => 'Analyse de qualité, sécurité et compatibilité du plugin WordPress {name}.',
    'plugin.wp_org_link'    => 'WordPress.org',
    'plugin.downloads'      => 'téléchargements',
    'plugin.active_installs' => 'installations actives',
    'plugin.version'        => 'Version',
    'plugin.updated'        => 'Mis à jour',
    'plugin.added'          => 'Ajouté',
    'plugin.no_data'        => '—',

    // Plugin detail — compatibility card
    'plugin.compat_title'         => 'Compatibilité & Prérequis',
    'plugin.compat_requires_wp'   => 'Requiert WordPress',
    'plugin.compat_tested'        => 'Testé jusqu\'à',
    'plugin.compat_requires_php'  => 'Requiert PHP',
    'plugin.compat_last_updated'  => 'Dernière mise à jour',
    'plugin.compat_dependencies'  => 'Dépendances de plugins',
    'plugin.compat_or_higher'     => 'ou supérieur',
    'plugin.compat_none'          => 'Aucune',
    'plugin.compat_badge_current' => 'Actuel',
    'plugin.compat_badge_recent'  => 'Récent',
    'plugin.compat_badge_outdated' => 'Obsolète',

    // Plugin detail — security card
    'plugin.security_title'       => 'Sécurité',
    'plugin.security_pending'     => 'L\'analyse de sécurité n\'est pas encore disponible pour ce plugin.',

    // Plugin detail — grade legend
    'plugin.grade_scale'      => 'Échelle de notes',
    'plugin.grade_a'          => 'Excellent',
    'plugin.grade_b'          => 'Bien',
    'plugin.grade_c'          => 'Acceptable',
    'plugin.grade_d'          => 'Médiocre',
    'plugin.grade_f'          => 'Insuffisant',
    'plugin.grade_pending'    => 'En attente',

    // Plugin not found
    'plugin.not_found_title'  => 'Plugin introuvable',
    'plugin.not_found_heading' => 'Plugin introuvable',
    'plugin.not_found_desc'   => 'Aucune donnée trouvée pour le plugin <code>{slug}</code>.',
    'plugin.not_found_back'   => 'Retour à l\'accueil',

    // About
    'about.page_title'        => 'À propos — WP Plugin Insights',
    'about.heading'           => 'À propos de WP Plugin Insights',
    'about.intro'             => 'PluginInsight est une plateforme indépendante qui analyse les plugins WordPress.org selon plusieurs dimensions de qualité et attribue des notes en lettres (A–F) pour donner aux développeurs, propriétaires de sites et équipes d\'hébergement un signal clair et actionnable.',
    'about.methodology_title' => 'Méthodologie de notation',
    'about.methodology_desc'  => 'Chaque plugin est évalué par dimension. La note globale reflète la note individuelle la plus basse, pondérée par gravité.',
    'about.col_grade'         => 'Note',
    'about.col_meaning'       => 'Signification',
    'about.grade_a_meaning'   => 'Excellent — aucun problème significatif',
    'about.grade_b_meaning'   => 'Bien — problèmes mineurs uniquement',
    'about.grade_c_meaning'   => 'Acceptable — quelques points à examiner',
    'about.grade_d_meaning'   => 'Médiocre — problèmes significatifs présents',
    'about.grade_f_meaning'   => 'Insuffisant — problèmes critiques ou plugin abandonné',
    'about.dimensions_title'  => 'Dimensions d\'analyse',
    'about.dim_compat'        => 'Compatibilité & Prérequis',
    'about.dim_compat_desc'   => 'Compatibilité des versions WordPress et PHP, date de dernière mise à jour.',
    'about.dim_security'      => 'Sécurité',
    'about.dim_security_desc' => 'CVE connus issus de WPScan / Patchstack / NVD, état des correctifs.',
    'about.dim_future'        => 'Qualité du code, traductions, performances, maintenance, licence — disponibles dans les phases futures.',
    'about.sources_title'     => 'Sources de données',
    'about.disclaimer'        => 'PluginInsight n\'est pas affilié à WordPress.org, à la WordPress Foundation ni à Automattic.',

    // 404
    '404.page_title'          => 'Page introuvable — WP Plugin Insights',
    '404.heading'             => 'Page introuvable',
    '404.desc'                => 'La page que vous recherchiez n\'existe pas.',
    '404.back'                => 'Aller à l\'accueil',
];
