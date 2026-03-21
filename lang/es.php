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

    // Plugin detail — grade legend
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
];
