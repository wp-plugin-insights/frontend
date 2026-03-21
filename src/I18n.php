<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Minimal i18n helper.
 *
 * Loads a PHP array from lang/<locale>.php and exposes translation lookups.
 * Supported locales are listed in SUPPORTED. Falls back to 'en' for unknown keys.
 */
class I18n
{
    /** @var array<string, string> */
    private array $strings = [];

    /** @var array<string, string> */
    private array $fallback = [];

    private readonly string $locale;

    /** @var list<string> */
    public const SUPPORTED = [
        'be', 'bg', 'ca', 'cs', 'da', 'de', 'el', 'en', 'es', 'et',
        'eu', 'fi', 'fr', 'ga', 'gl', 'hr', 'hu', 'it', 'lb', 'lld',
        'lt', 'lv', 'mt', 'nl', 'pl', 'pt', 'ro', 'ru', 'se', 'sk',
        'sl', 'sr', 'sv', 'tr', 'uk',
    ];

    public function __construct(string $locale)
    {
        $this->locale   = $this->sanitize($locale);
        $this->fallback = $this->loadFile('en');
        $this->strings  = $this->locale === 'en' ? $this->fallback : $this->loadFile($this->locale);
    }

    /**
     * Returns the active locale code.
     */
    public function locale(): string
    {
        return $this->locale;
    }

    /**
     * Returns the translated string for the given key.
     *
     * Variables in the form {name} are replaced with the corresponding value
     * from $vars. Values are NOT HTML-escaped here — callers must escape when
     * embedding in HTML.
     *
     * @param array<string, scalar> $vars
     */
    public function t(string $key, array $vars = []): string
    {
        $str = $this->strings[$key] ?? $this->fallback[$key] ?? $key;

        foreach ($vars as $name => $value) {
            $str = str_replace('{' . $name . '}', (string) $value, $str);
        }

        return $str;
    }

    /**
     * Formats an integer with locale-appropriate thousand separators.
     */
    public function number(int $value): string
    {
        return match ($this->locale) {
            'de'    => number_format($value, 0, ',', '.'),
            'es'    => number_format($value, 0, ',', '.'),
            default => number_format($value, 0, '.', ','),
        };
    }

    /**
     * Formats a MySQL datetime string (Y-m-d H:i:s) into a readable date.
     * Returns '—' for null or unparseable values.
     */
    public function date(?string $mysqlDatetime): string
    {
        if ($mysqlDatetime === null || $mysqlDatetime === '') {
            return '—';
        }

        $ts = strtotime($mysqlDatetime);
        if ($ts === false) {
            return '—';
        }

        return match ($this->locale) {
            'de'    => date('d.m.Y', $ts),
            default => date('j M Y', $ts),
        };
    }

    // -------------------------------------------------------------------------

    private function sanitize(string $locale): string
    {
        return in_array($locale, self::SUPPORTED, true) ? $locale : 'en';
    }

    /**
     * @return array<string, string>
     */
    private function loadFile(string $locale): array
    {
        $file = __DIR__ . '/../lang/' . $locale . '.php';

        if (!is_file($file)) {
            return [];
        }

        $data = require $file;

        return is_array($data) ? $data : [];
    }
}
