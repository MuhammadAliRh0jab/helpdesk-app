<?php

if (!function_exists('image')) {
    /**
     * Generate the URL to an image asset.
     *
     * @param string $path
     * @return string
     */
    function image($path)
    {
        return asset('assets/media/' . $path);
    }
}

if (!function_exists('printHtmlAttributes')) {
    /**
     * Print HTML attributes for a given tag.
     *
     * @param string $tag
     * @return string
     */
    function printHtmlAttributes($tag)
    {
        $attributes = [];
        if ($tag === 'html') {
            $attributes['class'] = 'h-100';
        } elseif ($tag === 'body') {
            $attributes['id'] = 'kt_body';
            $attributes['class'] = 'app-default';
        }
        return collect($attributes)
            ->map(function ($value, $key) {
                return sprintf('%s="%s"', $key, $value);
            })
            ->implode(' ');
    }
}

if (!function_exists('printHtmlClasses')) {
    /**
     * Print HTML classes for a given tag.
     *
     * @param string $tag
     * @return string
     */
    function printHtmlClasses($tag)
    {
        $classes = [];
        if ($tag === 'body') {
            $classes[] = 'h-100';
        }
        return sprintf('class="%s"', implode(' ', $classes));
    }
}

if (!function_exists('includeFavicon')) {
    /**
     * Include favicon HTML.
     *
     * @return string
     */
    function includeFavicon()
    {
        return '<link rel="shortcut icon" href="' . asset('assets/media/logos/favicon.ico') . '" />';
    }
}

if (!function_exists('includeFonts')) {
    /**
     * Include font imports.
     *
     * @return string
     */
    function includeFonts()
    {
        return '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />';
    }
}

if (!function_exists('getGlobalAssets')) {
    /**
     * Get global assets (CSS or JS).
     *
     * @param string $type
     * @return array
     */
    function getGlobalAssets($type = 'js')
    {
        if ($type === 'css') {
            return [
                'assets/css/style.bundle.css',
            ];
        }
        return [
            'assets/js/scripts.bundle.js',
        ];
    }
}

if (!function_exists('getVendors')) {
    /**
     * Get vendor assets (CSS or JS).
     *
     * @param string $type
     * @return array
     */
    function getVendors($type)
    {
        if ($type === 'css') {
            return [];
        }
        return [
            'assets/js/vendors.js', // Sesuaikan jika Anda memiliki vendor JS seperti Toastr atau SweetAlert
        ];
    }
}

if (!function_exists('getCustomCss')) {
    /**
     * Get custom CSS assets.
     *
     * @return array
     */
    function getCustomCss()
    {
        return [];
    }
}

if (!function_exists('getCustomJs')) {
    /**
     * Get custom JS assets.
     *
     * @return array
     */
    function getCustomJs()
    {
        return [];
    }
}