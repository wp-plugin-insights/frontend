<?php

/**
 * Spanish (es) translations.
 */

declare(strict_types=1);

return [
    // Navigation & chrome
    'nav.home'              => 'Inicio',
    'nav.about'             => 'Acerca de',
    'theme.to_light'        => 'Cambiar a modo claro',
    'theme.to_dark'         => 'Cambiar a modo oscuro',

    // Footer
    'footer.tagline'        => 'PluginInsight — señales de calidad independientes para plugins de WordPress.',
    'footer.disclaimer'     => 'No está afiliado con WordPress.org ni con Automattic.',

    // Homepage
    'home.page_title'       => 'WP Plugin Insights — Directorio de calidad de plugins de WordPress',
    'home.meta_desc'        => 'Analiza la calidad, seguridad y mantenimiento de cualquier plugin de WordPress.org. Notas por letras de un vistazo.',
    'home.headline'         => 'Calidad de plugins de WordPress de un vistazo',
    'home.subheadline'      => 'Busca cualquier plugin de WordPress.org y obtén un informe claro de calidad y seguridad.',
    'home.search_placeholder' => 'Slug o nombre del plugin — p. ej. woocommerce',
    'home.search_btn'       => 'Analizar',
    'home.recently_reviewed' => 'Revisados recientemente',
    'home.updated_on'       => 'Actualizado',
    'home.no_plugins'       => 'Todavía no hay plugins en la base de datos. Vuelve pronto.',
    'home.db_count'         => '{count} plugins en la base de datos',

    // Plugin detail — header
    'plugin.page_title'     => '{name} — WP Plugin Insights',
    'plugin.meta_desc'      => 'Análisis de calidad, seguridad y compatibilidad del plugin de WordPress {name}.',
    'plugin.wp_org_link'    => 'WordPress.org',
    'plugin.downloads'      => 'descargas',
    'plugin.active_installs' => 'instalaciones activas',
    'plugin.version'        => 'Versión',
    'plugin.updated'        => 'Actualizado',
    'plugin.added'          => 'Añadido',
    'plugin.no_data'        => '—',

    // Plugin detail — compatibility card
    'plugin.compat_title'         => 'Compatibilidad y requisitos',
    'plugin.compat_requires_wp'   => 'Requiere WordPress',
    'plugin.compat_tested'        => 'Probado hasta',
    'plugin.compat_requires_php'  => 'Requiere PHP',
    'plugin.compat_last_updated'  => 'Última actualización',
    'plugin.compat_dependencies'  => 'Dependencias de plugins',
    'plugin.compat_or_higher'     => 'o superior',
    'plugin.compat_none'          => 'Ninguna',
    'plugin.compat_badge_current' => 'Actual',
    'plugin.compat_badge_recent'  => 'Reciente',
    'plugin.compat_badge_outdated' => 'Desactualizado',

    // Plugin detail — security card
    'plugin.security_title'       => 'Seguridad',
    'plugin.security_pending'     => 'El análisis de seguridad todavía no está disponible para este plugin.',

    // Plugin detail — overall grade + grade legend
    'plugin.overall_grade'    => 'Nota global',
    'plugin.grade_scale'      => 'Escala de notas',
    'plugin.grade_a'          => 'Excelente',
    'plugin.grade_b'          => 'Bueno',
    'plugin.grade_c'          => 'Aceptable',
    'plugin.grade_d'          => 'Deficiente',
    'plugin.grade_f'          => 'Suspenso',
    'plugin.grade_pending'    => 'Pendiente',

    // Plugin not found
    'plugin.not_found_title'  => 'Plugin no encontrado',
    'plugin.not_found_heading' => 'Plugin no encontrado',
    'plugin.not_found_desc'   => 'No se encontraron datos para el plugin <code>{slug}</code>.',
    'plugin.not_found_back'   => 'Volver al inicio',

    // About
    'about.page_title'        => 'Acerca de — WP Plugin Insights',
    'about.heading'           => 'Acerca de WP Plugin Insights',
    'about.intro'             => 'PluginInsight es una plataforma independiente que analiza plugins de WordPress.org en múltiples dimensiones de calidad y asigna notas por letras (A–F) para ofrecer a desarrolladores, propietarios de sitios y equipos de alojamiento una señal clara y accionable.',
    'about.methodology_title' => 'Metodología de calificación',
    'about.methodology_desc'  => 'Cada plugin se evalúa por dimensión. La nota global refleja la puntuación individual más baja, ponderada por gravedad.',
    'about.col_grade'         => 'Nota',
    'about.col_meaning'       => 'Significado',
    'about.grade_a_meaning'   => 'Excelente — sin problemas significativos',
    'about.grade_b_meaning'   => 'Bueno — solo problemas menores',
    'about.grade_c_meaning'   => 'Aceptable — algunas preocupaciones a revisar',
    'about.grade_d_meaning'   => 'Deficiente — problemas significativos presentes',
    'about.grade_f_meaning'   => 'Suspenso — problemas críticos o plugin abandonado',
    'about.dimensions_title'  => 'Dimensiones de análisis',
    'about.dim_compat'        => 'Compatibilidad y requisitos',
    'about.dim_compat_desc'   => 'Compatibilidad de versiones de WordPress y PHP, fecha de última actualización.',
    'about.dim_security'      => 'Seguridad',
    'about.dim_security_desc' => 'CVEs conocidos de WPScan / Patchstack / NVD, estado de parches.',
    'about.dim_future'        => 'Calidad del código, traducciones, rendimiento, mantenimiento, licencia — disponibles en fases futuras.',
    'about.sources_title'     => 'Fuentes de datos',
    'about.disclaimer'        => 'PluginInsight no está afiliado con WordPress.org, la WordPress Foundation ni con Automattic.',

    // 404
    '404.page_title'          => 'Página no encontrada — WP Plugin Insights',
    '404.heading'             => 'Página no encontrada',
    '404.desc'                => 'La página que buscabas no existe.',
    '404.back'                => 'Ir al inicio',
    'auth.register_title'             => 'Crear cuenta — WP Plugin Insights',
    'auth.register_heading'           => 'Crear una cuenta',
    'auth.register_btn'               => 'Crear cuenta',
    'auth.register_have_account'      => '¿Ya tienes cuenta?',
    'auth.register_login_link'        => 'Iniciar sesión',
    'auth.register_err_email_taken'   => 'Esa dirección de correo electrónico ya está registrada.',
    'auth.register_err_invalid_email' => 'Por favor, introduce una dirección de correo electrónico válida.',
    'auth.register_success'           => 'Cuenta creada. Ya puedes iniciar sesión.',
    'auth.login_register'             => 'Crear cuenta',
    'auth.login_new_here'             => '¿Nuevo aquí?',
    'plugin.author' => 'Autor',
    'plugin.source' => 'Fuente',

    // Plugin detail — Compatibility & Requirements grade notes
    'plugin.compat_note_no_wp'           => 'No se declara una versión mínima de WordPress',
    'plugin.compat_note_no_php'          => 'No se declara una versión mínima de PHP',
    'plugin.compat_note_php_wp_mismatch' => 'El mínimo de PHP declarado es inferior al que requiere la versión de WordPress indicada',
    'plugin.compat_note_outdated'        => 'El plugin no se ha actualizado en más de un año',
    'plugin.compat_note_tested_outdated' => 'El plugin no ha sido probado con la versión actual de WordPress',

    // Plugin detail — WP–PHP compatibility row
    'plugin.wp_php_compat'      => 'Compatibilidad WP–PHP',
    'plugin.wp_php_compat_info' => 'WP {wp}+ requiere PHP {php}+. Este plugin no declara una versión mínima de PHP.',
    'plugin.wp_php_compat_warn' => 'WP {wp}+ requiere PHP {php}+, pero este plugin solo declara PHP {declared}+. El mínimo de PHP declarado puede ser demasiado bajo.',
    'plugin.wp_php_compat_ok'   => 'PHP {declared}+ es compatible con WP {wp}+ (que requiere PHP {php}+).',

    // Runner cards — PHP compatibility block
    'runner.php_declared'           => 'PHP declarado',
    'runner.php_detected'           => 'PHP detectado',
    'runner.php_tested_versions'    => 'Versiones probadas',
    'runner.php_effective_min'      => 'Mínimo efectivo: PHP {version}+.',
    'runner.php_grade_note_match'   => 'PHP declarado {declared}+ coincide con el mínimo efectivo ({effective}+)',
    'runner.php_grade_note_over'    => 'PHP declarado {declared}+ supera el mínimo efectivo ({effective}+) — restricción innecesaria',
    'runner.php_grade_note_under'   => 'PHP declarado {declared}+ es inferior al mínimo efectivo ({effective}+) — declaración incorrecta',

    // Runner cards — wp-since block
    'runner.wp_declared_min'        => 'WP mínimo declarado',
    'runner.wp_suggested_min'       => 'WP mínimo sugerido',
    'runner.tool_output'            => 'Salida de herramienta',

    // Runner cards — translate block
    'runner.locales_detected'       => 'Idiomas detectados',
    'runner.locales_compliant'      => 'Conformes (≥80%)',
    'runner.untranslated_strings'   => 'Cadenas sin traducir',
    'runner.text_domain'            => 'Dominio de texto',
    'runner.load_textdomain'        => 'load_plugin_textdomain',
    'runner.js_strings_translated'  => 'Cadenas JS traducidas',
    'runner.supported_locales'      => 'Idiomas admitidos',
    'runner.no_locale_coverage'     => 'Ningún idioma alcanza el 80% de cobertura.',
    'runner.coverage_by_locale'     => 'Cobertura por idioma',
    'runner.locale_col'             => 'Idioma',
    'runner.language_col'           => 'Nombre',
    'runner.coverage_col'           => 'Cobertura',
    'runner.td_declared'            => 'Declarado',
    'runner.td_expected'            => 'Esperado',
    'runner.td_usage'               => 'Uso encontrado',
    'runner.td_valid'               => 'válido',
    'runner.td_invalid'             => 'inválido',
    'runner.untranslated_preview'   => 'Cadenas sin traducir (primeras {count})',
    'runner.str_col'                => 'Cadena',
    'runner.file_col'               => 'Archivo',
    'runner.line_col'               => 'Línea',
    'runner.severity_high'          => 'alto',
    'runner.severity_medium'        => 'medio',
    'runner.severity_low'           => 'bajo',
    'runner.severity_trivial'       => 'trivial',

    // Runner cards — hooks block
    'runner.hooks_total_used'           => 'Hooks utilizados',
    'runner.hooks_total_provided'       => 'Hooks proporcionados',
    'runner.hooks_provides'             => 'Proporciona hooks',
    'runner.hooks_extensible'           => 'Extensible',
    'runner.hooks_breakdown'            => 'Desglose de hooks',
    'runner.hooks_col_unique'           => 'Únicos',
    'runner.hooks_col_total'            => 'Total',
    'runner.hooks_actions_used'         => 'Actions utilizadas',
    'runner.hooks_filters_used'         => 'Filters utilizados',
    'runner.hooks_actions_provided'     => 'Actions proporcionadas',
    'runner.hooks_filters_provided'     => 'Filters proporcionados',
    'runner.hooks_wp_actions_used'      => 'Principales actions de WordPress usadas',
    'runner.hooks_plugin_actions_used'  => 'Principales actions del plugin usadas',
    'runner.hooks_wp_filters_used'      => 'Principales filters de WordPress usados',
    'runner.hooks_plugin_filters_used'  => 'Principales filters del plugin usados',
    'runner.hooks_col_hook'             => 'Hook',
    'runner.hooks_col_count'            => 'Usos',
    'runner.hooks_col_locations'        => 'Ubicaciones',
    'runner.hooks_provided_title'       => 'Hooks proporcionados',
    'runner.hooks_show_all'             => 'Ver todos',
    'runner.hooks_more'                 => 'más',

    // Runner cards — translate grade adjustment notes
    'runner.tr_note_no_textdomain'      => 'No se declara ningún dominio de texto — nota penalizada −2',
    'runner.tr_note_invalid_textdomain' => 'El dominio de texto es inválido o no coincide — nota penalizada −1',
    'runner.tr_note_high'               => '{count} incidencia(s) de gravedad alta — nota penalizada −1',
    'runner.tr_note_many_high'          => '{count} incidencias de gravedad alta — nota penalizada −2',
    'runner.tr_note_many_medium'        => '{count} incidencias de gravedad media — nota penalizada −1',
];
