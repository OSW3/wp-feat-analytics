<?php
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics\Services;

final class ConfigService
{
    public static function get(string $key): ?string
    {
        // 1. Constante définie (ex: define('ANALYTICS_GA4_ID', 'G-XXX') dans wp-config.php)
        if (defined($key)) {
            $val = constant($key);
            return is_string($val) && !empty($val) ? $val : null;
        }

        // 2. Variable d'environnement ($_ENV ou getenv)
        if (isset($_ENV[$key]) && is_string($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }

        $envValue = getenv($key);
        if ($envValue !== false && $envValue !== '') {
            return $envValue;
        }

        // 3. Option WordPress (ex: option_name = 'wp_feat_analytics_ga4_id')
        $optionKey = 'wp_feat_analytics_' . strtolower(str_replace('ANALYTICS_', '', $key));
        $dbValue = get_option($optionKey);

        if (is_string($dbValue) && !empty($dbValue)) {
            return $dbValue;
        }

        return null;
    }
}