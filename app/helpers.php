<?php

use App\Models\PageContent;

if (! function_exists('konten')) {
    function konten(string $page, string $section, string $key, string $default = ''): string
    {
        static $cache = [];

        $page = trim($page);
        $section = trim($section);
        $key = trim($key);

        $cacheKey = "{$page}|{$section}|{$key}";

        if (array_key_exists($cacheKey, $cache)) {
            return (string) $cache[$cacheKey];
        }

        try {
            $value = PageContent::query()
                ->where('page', $page)
                ->where('section', $section)
                ->where('key', $key)
                ->value('value');
        } catch (\Throwable) {
            $value = null;
        }

        $cache[$cacheKey] = $value ?? $default;

        return (string) $cache[$cacheKey];
    }
}
