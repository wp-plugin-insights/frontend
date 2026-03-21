<?php

/**
 * English translations.
 */

declare(strict_types=1);

return [
    // Navigation & chrome
    'nav.home'              => 'Home',
    'nav.about'             => 'About',
    'theme.to_light'        => 'Switch to light mode',
    'theme.to_dark'         => 'Switch to dark mode',

    // Footer
    'footer.tagline'        => 'PluginInsight — independent quality signals for WordPress plugins.',
    'footer.disclaimer'     => 'Not affiliated with WordPress.org or Automattic.',

    // Homepage
    'home.page_title'       => 'WP Plugin Insights — WordPress Plugin Quality Directory',
    'home.meta_desc'        => 'Analyse the quality, security, and maintenance of any WordPress.org plugin. Letter grades at a glance.',
    'home.headline'         => 'WordPress Plugin Quality at a Glance',
    'home.subheadline'      => 'Search any plugin from WordPress.org and get a clear quality & security report.',
    'home.search_placeholder' => 'Plugin slug or name — e.g. woocommerce',
    'home.search_btn'       => 'Analyse',
    'home.recently_reviewed' => 'Recently Reviewed',
    'home.updated_on'       => 'Updated',
    'home.analysed_on'      => 'Analysed',
    'home.no_plugins'       => 'No plugins in the database yet. Check back soon.',
    'home.db_count'         => '{count} plugins in the database',

    // Plugin detail — header
    'plugin.page_title'     => '{name} — WP Plugin Insights',
    'plugin.meta_desc'      => 'Quality, security, and compatibility analysis for the {name} WordPress plugin.',
    'plugin.wp_org_link'    => 'WordPress.org',
    'plugin.downloads'      => 'downloads',
    'plugin.active_installs' => 'active installs',
    'plugin.version'             => 'Version',
    'plugin.version_picker_label' => 'Select analysis version',
    'plugin.version_current'      => 'current',
    'plugin.updated'        => 'Updated',
    'plugin.added'          => 'Added',
    'plugin.no_data'        => '—',

    // Plugin detail — compatibility card
    'plugin.compat_title'         => 'Compatibility & Requirements',
    'plugin.compat_requires_wp'   => 'Requires WordPress',
    'plugin.compat_tested'        => 'Tested up to',
    'plugin.compat_requires_php'  => 'Requires PHP',
    'plugin.compat_last_updated'  => 'Last updated',
    'plugin.compat_dependencies'  => 'Plugin dependencies',
    'plugin.compat_or_higher'     => 'or higher',
    'plugin.compat_none'          => 'None',
    'plugin.compat_badge_current' => 'Current',
    'plugin.compat_badge_recent'  => 'Recent',
    'plugin.compat_badge_outdated' => 'Outdated',

    // Plugin detail — analysis cards (runner results)
    'plugin.analysis_title'   => 'Analysis',
    'plugin.analysis_pending' => 'Analysis results are not yet available for this plugin version.',
    'plugin.grade_label'      => 'Grade: {grade}',
    'plugin.score_label'      => 'Score',
    'plugin.metrics_title'    => 'Metrics',
    'plugin.issues_title'     => 'Issues',
    'plugin.analysed_on'      => 'Analysed on',

    // Plugin detail — grade legend
    'plugin.grade_scale'      => 'Grade scale',
    'plugin.grade_a'          => 'Excellent',
    'plugin.grade_b'          => 'Good',
    'plugin.grade_c'          => 'Acceptable',
    'plugin.grade_d'          => 'Poor',
    'plugin.grade_f'          => 'Failing',
    'plugin.grade_pending'    => 'Pending',

    'plugin.author'           => 'Author',
    'plugin.source'           => 'Source',

    // Plugin not found
    'plugin.not_found_title'  => 'Plugin not found',
    'plugin.not_found_heading' => 'Plugin not found',
    'plugin.not_found_desc'   => 'No data found for plugin <code>{slug}</code>.',
    'plugin.not_found_back'   => 'Back to home',

    // About
    'about.page_title'        => 'About — WP Plugin Insights',
    'about.heading'           => 'About WP Plugin Insights',
    'about.intro'             => 'PluginInsight is an independent platform that analyses WordPress.org plugins across multiple quality dimensions and assigns letter grades (A–F) to give developers, site owners, and hosting teams a clear, actionable signal.',
    'about.methodology_title' => 'Grading methodology',
    'about.methodology_desc'  => 'Each plugin is evaluated per dimension. The overall grade reflects the lowest individual dimension score, weighted by severity.',
    'about.col_grade'         => 'Grade',
    'about.col_meaning'       => 'Meaning',
    'about.grade_a_meaning'   => 'Excellent — no significant issues',
    'about.grade_b_meaning'   => 'Good — minor issues only',
    'about.grade_c_meaning'   => 'Acceptable — some concerns worth reviewing',
    'about.grade_d_meaning'   => 'Poor — significant issues present',
    'about.grade_f_meaning'   => 'Failing — critical issues or abandoned plugin',
    'about.dimensions_title'  => 'Analysis dimensions',
    'about.dim_compat'        => 'Compatibility & Requirements',
    'about.dim_compat_desc'   => 'WordPress and PHP version compatibility, last update date.',
    'about.dim_security'      => 'Security',
    'about.dim_security_desc' => 'Known CVEs from WPScan / Patchstack / NVD, patch status.',
    'about.dim_future'        => 'Code Quality, Translations, Performance, Maintenance, License — coming in future phases.',
    'about.sources_title'     => 'Data sources',
    'about.disclaimer'        => 'PluginInsight is not affiliated with WordPress.org, the WordPress Foundation, or Automattic.',

    // 404
    '404.page_title'          => 'Page not found — WP Plugin Insights',
    '404.heading'             => 'Page not found',
    '404.desc'                => 'The page you were looking for does not exist.',
    '404.back'                => 'Go to homepage',

    // Navigation — auth
    'nav.account'             => 'Account',
    'nav.logout'              => 'Log out',

    // Login
    'auth.login_title'        => 'Log in — WP Plugin Insights',
    'auth.login_heading'      => 'Log in',
    'auth.login_email'        => 'E-mail address',
    'auth.login_password'     => 'Password',
    'auth.login_btn'          => 'Log in',
    'auth.login_forgot'       => 'Forgot password?',
    'auth.login_error'        => 'Incorrect e-mail or password.',
    'auth.login_locked'       => 'Too many failed attempts. Please try again later.',
    'auth.login_no_account'   => 'Don\'t have an account? Contact an administrator.',

    // Forgot password
    'auth.forgot_title'       => 'Forgot password — WP Plugin Insights',
    'auth.forgot_heading'     => 'Forgot your password?',
    'auth.forgot_desc'        => 'Enter your e-mail address and we\'ll send you a reset link.',
    'auth.forgot_email'       => 'E-mail address',
    'auth.forgot_btn'         => 'Send reset link',
    'auth.forgot_sent'        => 'If that address is registered, a reset link is on its way. Check your inbox.',

    // Reset password
    'auth.reset_title'        => 'Reset password — WP Plugin Insights',
    'auth.reset_heading'      => 'Set a new password',
    'auth.reset_password'     => 'New password',
    'auth.reset_confirm'      => 'Confirm new password',
    'auth.reset_btn'          => 'Set new password',
    'auth.reset_invalid'      => 'This reset link is invalid or has expired.',
    'auth.reset_mismatch'     => 'Passwords do not match.',
    'auth.reset_too_short'    => 'Password must be at least 12 characters.',
    'auth.reset_success'      => 'Password updated. You can now log in.',

    // Account
    'auth.account_title'          => 'Account — WP Plugin Insights',
    'auth.account_heading'        => 'Your account',
    'auth.account_email'          => 'E-mail address',
    'auth.account_pw_section'     => 'Change password',
    'auth.account_pw_current'     => 'Current password',
    'auth.account_pw_new'         => 'New password',
    'auth.account_pw_confirm'     => 'Confirm new password',
    'auth.account_pw_btn'         => 'Update password',
    'auth.account_profile_section' => 'Profile',
    'auth.account_name'           => 'Display name',
    'auth.account_name_hint'      => 'Optional.',
    'auth.account_profile_btn'    => 'Save profile',
    'auth.account_lang_section'   => 'Language',
    'auth.account_lang'           => 'Preferred language',
    'auth.account_lang_auto'      => 'Auto-detect',
    'auth.account_lang_btn'       => 'Save language',
    'auth.account_ok_password'    => 'Password updated.',
    'auth.account_ok_profile'     => 'Profile saved.',
    'auth.account_ok_lang'        => 'Language preference saved.',
    'auth.account_err_current_pw' => 'Current password is incorrect.',
    'auth.account_err_mismatch'   => 'Passwords do not match.',
    'auth.account_err_too_short'  => 'Password must be at least 12 characters.',

    // Register
    'auth.register_title'             => 'Create account — WP Plugin Insights',
    'auth.register_heading'           => 'Create an account',
    'auth.register_btn'               => 'Create account',
    'auth.register_have_account'      => 'Already have an account?',
    'auth.register_login_link'        => 'Log in',
    'auth.register_err_email_taken'   => 'That e-mail address is already registered.',
    'auth.register_err_invalid_email' => 'Please enter a valid e-mail address.',
    'auth.register_success'           => 'Account created. You can now log in.',
    'auth.login_register'             => 'Create account',
    'auth.login_new_here'             => 'New here?',
];
