/**
 * PluginInsight — main JavaScript (vanilla, no dependencies)
 */

'use strict';

/* ── Light / dark mode ───────────────────────────────────── */
(function () {
    const STORAGE_KEY = 'pi-theme';
    const html = document.documentElement;
    const btn  = document.getElementById('theme-toggle');

    function applyTheme(theme) {
        html.setAttribute('data-bs-theme', theme);
        if (btn) {
            // Read labels from data attributes so they work for all languages
            const labelLight = btn.dataset.labelLight || 'Switch to light mode';
            const labelDark  = btn.dataset.labelDark  || 'Switch to dark mode';
            btn.setAttribute('aria-label', theme === 'dark' ? labelLight : labelDark);
            btn.querySelector('.theme-icon-light').classList.toggle('d-none', theme === 'dark');
            btn.querySelector('.theme-icon-dark').classList.toggle('d-none', theme !== 'dark');
        }
    }

    function toggleTheme() {
        const current = html.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
        const next    = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem(STORAGE_KEY, next);
        applyTheme(next);
    }

    // Initialise from storage or system preference
    const stored   = localStorage.getItem(STORAGE_KEY);
    const prefDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(stored || (prefDark ? 'dark' : 'light'));

    if (btn) {
        btn.addEventListener('click', toggleTheme);
    }

    // Keep in sync when the OS preference changes and no explicit choice is stored
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        if (!localStorage.getItem(STORAGE_KEY)) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
}());

/* ── Analysis card expand / collapse ─────────────────────── */
/*
 * Bootstrap's collapse plugin handles click + ARIA on <button> elements
 * automatically. This block only syncs the .toggle-icon rotation class.
 */
(function () {
    document.querySelectorAll('.analysis-card .collapse').forEach(function (panel) {
        panel.addEventListener('show.bs.collapse', function () {
            var btn = document.querySelector('[data-bs-target="#' + panel.id + '"]');
            if (btn) { btn.setAttribute('aria-expanded', 'true'); }
        });
        panel.addEventListener('hide.bs.collapse', function () {
            var btn = document.querySelector('[data-bs-target="#' + panel.id + '"]');
            if (btn) { btn.setAttribute('aria-expanded', 'false'); }
        });
    });
}());

/* ── Language switcher: scroll active item into view ──────── */
(function () {
    var scroller = document.querySelector('.lang-scroll');
    if (!scroller) { return; }
    var active = scroller.querySelector('.dropdown-item.active');
    if (active) {
        // On dropdown open, scroll the selected language into view
        scroller.closest('.dropdown').addEventListener('shown.bs.dropdown', function () {
            active.scrollIntoView({ block: 'nearest' });
            active.focus();
        });
    }
}());
