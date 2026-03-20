/**
 * PluginInsight — main JavaScript (vanilla, no dependencies)
 */

'use strict';

/* ── Light / dark mode ───────────────────────────────────── */
(function () {
    const STORAGE_KEY = 'pi-theme';
    const html = document.documentElement;
    const btn  = document.getElementById('theme-toggle');

    function getStored() {
        return localStorage.getItem(STORAGE_KEY);
    }

    function applyTheme(theme) {
        html.setAttribute('data-bs-theme', theme);
        if (btn) {
            btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
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
    const stored   = getStored();
    const prefDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(stored || (prefDark ? 'dark' : 'light'));

    if (btn) {
        btn.addEventListener('click', toggleTheme);
    }
}());

/* ── Analysis card expand / collapse ─────────────────────── */
(function () {
    document.querySelectorAll('.analysis-card .card-header').forEach(function (header) {
        header.addEventListener('click', function () {
            const target = document.querySelector(header.dataset.bsTarget || header.getAttribute('data-bs-target'));
            if (!target) return;
            const isExpanded = header.getAttribute('aria-expanded') === 'true';
            header.setAttribute('aria-expanded', String(!isExpanded));
            header.classList.toggle('collapsed', isExpanded);
        });
    });
}());

/* ── Homepage search (stub for Phase 1) ─────────────────── */
(function () {
    const form = document.getElementById('plugin-search-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const slug = form.querySelector('input[name="q"]').value.trim().toLowerCase().replace(/\s+/g, '-');
        if (slug) {
            window.location.href = 'plugin.html?slug=' + encodeURIComponent(slug);
        }
    });
}());
