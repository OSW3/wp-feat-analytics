<?php
declare(strict_types=1);

namespace OSW3\WpFeatAnalytics\Guards;

use OSW3\WpFeatAnalytics\Module;
use OSW3\WpFeatAnalytics\Interfaces\GuardInterface;

final class DependenciesGuard implements GuardInterface
{
    public static function check(): array
    {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $issues = [];

        foreach (Module::DEPENDENCIES as $pluginFile => $pluginName) {
            $fullPath = WP_PLUGIN_DIR . '/' . $pluginFile;

            // 1. Le plugin est-il présent sur le disque ?
            if (!file_exists($fullPath)) {
                $issues[] = sprintf(
                    __('Le plugin "%s" est requis. Veuillez l\'installer.', Module::getDomain()),
                    $pluginName
                );
                continue;
            }

            // 2. Tenter l'auto-activation s'il n'est pas actif
            if (!is_plugin_active($pluginFile)) {
                $result = activate_plugin($pluginFile, '', false, true);

                if (is_wp_error($result)) {
                    $issues[] = sprintf(
                        __('Impossible d\'activer automatiquement "%s" : %s', Module::getDomain()),
                        $pluginName,
                        $result->get_error_message()
                    );
                }
            }
        }

        return $issues;
    }
}