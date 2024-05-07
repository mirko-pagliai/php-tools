<?php
declare(strict_types=1);

/**
 * This file is part of php-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */

if (!function_exists('clean_url')) {
    /**
     * Cleans an url. It removes all unnecessary parts, as fragment (#), trailing slash and `www` prefix
     * @param string $url Url
     * @param bool $removeWWW Removes the www prefix
     * @param bool $removeTrailingSlash Removes the trailing slash
     * @return string
     * @since 1.0.3
     * @deprecated `clean_url()` is deprecated and will be removed in a future release
     */
    function clean_url(string $url, bool $removeWWW = false, bool $removeTrailingSlash = false): string
    {
        trigger_deprecation('php-tools', '1.9.4', '`clean_url()` is deprecated and will be removed in a future release');

        $url = preg_replace('/(#.*)$/', '', $url) ?: '';
        if ($removeWWW) {
            $url = preg_replace('/^((http|https|ftp):\/\/)?www\./', '$1', $url) ?: '';
        }

        return $removeTrailingSlash ? rtrim($url, '/') : $url;
    }
}

if (!function_exists('get_hostname_from_url')) {
    /**
     * Gets the host name from an url.
     *
     * It also removes the `www` prefix.
     * @param string $url Url
     * @return string
     * @since 1.0.2
     * @deprecated `get_hostname_from_url()` is deprecated and will be removed in a future release
     */
    function get_hostname_from_url(string $url): string
    {
        trigger_deprecation('php-tools', '1.9.4', '`get_hostname_from_url()` is deprecated and will be removed in a future release');

        $host = parse_url($url, PHP_URL_HOST) ?: '';

        return str_starts_with($host, 'www.') ? substr($host, 4) : $host;
    }
}

if (!function_exists('is_external_url')) {
    /**
     * Checks if an url is external, relative to the passed hostname
     * @param string $url Url to check
     * @param string $hostname Hostname for the comparison
     * @return bool
     * @since 1.0.4
     * @deprecated `is_external_url()` is deprecated and will be removed in a future release
     */
    function is_external_url(string $url, string $hostname): bool
    {
        trigger_deprecation('php-tools', '1.9.4', '`is_external_url()` is deprecated and will be removed in a future release');

        $hostForUrl = get_hostname_from_url($url);

        //Url with the same host and relative url are not external
        return $hostForUrl && strcasecmp($hostForUrl, $hostname) !== 0;
    }
}

if (!function_exists('is_localhost')) {
    /**
     * Checks if it's localhost
     * @return bool
     * @since 1.3.3
     * @deprecated `is_localhost()` is deprecated and will be removed in a future release
     */
    function is_localhost(): bool
    {
        trigger_deprecation('php-tools', '1.9.4', '`is_localhost()` is deprecated and will be removed in a future release');

        return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
    }
}

if (!function_exists('is_url')) {
    /**
     * Checks if a string is a valid url
     * @param string $string String
     * @return bool
     */
    function is_url(string $string): bool
    {
        return (bool)preg_match("/^\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;()]*[-a-z0-9+&@#\/%=~_|()]$/i", $string);
    }
}
