<?php

declare(strict_types=1);

namespace OSW3\WpFeatAnalytics\Guards;

use OSW3\WpFeatAnalytics\Module;
use OSW3\WpFeatAnalytics\Interfaces\GuardInterface;

final class EnvironmentGuard implements GuardInterface
{
    public static function check(): array
    {
        $issues = [];

        // Check for PHP version compatibility
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $issues[] = sprintf(
                __('%s requires PHP 8.2 or newer. Current version: %s.', Module::getDomain()),
                Module::getName(),
                PHP_VERSION
            );
        }

        // Check for WordPress version compatibility
        if (isset($GLOBALS['wp_version']) && version_compare((string) $GLOBALS['wp_version'], '6.5', '<')) {
            $issues[] = sprintf(
                __('%s requires WordPress 6.5 or newer. Current version: %s.', Module::getDomain()),
                Module::getName(),
                (string) $GLOBALS['wp_version']
            );
        }

        return $issues;
    }
}