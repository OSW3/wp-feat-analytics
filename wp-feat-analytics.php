<?php
/**
 * Plugin Name: WP Feat : Analytics
 * Plugin URI: https://github.com/OSW3/wp-feat-analytics
 * Description: Initialise et configure la structure de base d'un site WordPress pour l'analytics.
 * Version: 1.0.0
 * Author: OSW3
 * Author URI: https://github.com/OSW3
 * License: MIT
 * Text Domain: wp-feat-analytics
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}


// Autoload dependencies using Composer
// --

$autoload = __DIR__ . '/vendor/autoload.php';

if (!is_file($autoload)) {
    add_action('admin_notices', static function (): void {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__(sprintf('%s: Composer autoload is missing. Run composer install in the plugin directory.', \OSW3\WpFeatAnalytics\Module::getName()), \OSW3\WpFeatAnalytics\Module::getDomain());
        echo '</p></div>';
    });

    return;
}

require_once $autoload;


// Hook & Bootstrap the module
// --

// Hook d'activation
register_activation_hook(__FILE__, static function (): void {
    if (class_exists(\OSW3\WpFeatAnalytics\Support\Activator::class)) {
        \OSW3\WpFeatAnalytics\Support\Activator::activate();
    }
});

// Hook de désactivation
register_deactivation_hook(__FILE__, static function (): void {
    if (class_exists(\OSW3\WpFeatAnalytics\Support\Deactivator::class)) {
        \OSW3\WpFeatAnalytics\Support\Deactivator::deactivate();
    }
});

// Enregistrement de la carte d'identité auprès de wp-site-core
add_filter('wp_site_core_register_modules', static function (array $modules): array {
    $modules[(string) \OSW3\WpFeatAnalytics\Module::getId()] = \OSW3\WpFeatAnalytics\Module::class;
    return $modules;
});

// Déclaration des dépendances
add_filter('wp_site_dependencies', static function (array $deps): array {
    $currentPlugin = plugin_basename(__FILE__);
    foreach (\OSW3\WpFeatAnalytics\Module::DEPENDENCIES as $pluginFile => $pluginName) {
        $deps[$pluginFile][$currentPlugin] = \OSW3\WpFeatAnalytics\Module::getName();
    }
    return $deps;
});

// Demarrage du module après le chargement de tous les plugins
add_action('plugins_loaded', static function (): void {

    // 1. Vérification que le Core est bien présent et chargé
    if (!class_exists(\OSW3\WpSiteCore\Module::class)) {
        add_action('admin_notices', static function (): void {
            printf(
                '<div class="notice notice-error"><p>%s</p></div>',
                esc_html__(sprintf('%s a été désactivé car WP Site Core est introuvable ou inactif.', \OSW3\WpFeatAnalytics\Module::getName()), \OSW3\WpFeatAnalytics\Module::getDomain())
            );
        });

        // Autodésactivation de sécurité pour éviter le WSOD
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        deactivate_plugins(plugin_basename(__FILE__));
        return;
    }

    // 2. Si le Core est là, on démarre le module normalement
    \OSW3\WpFeatAnalytics\Module::init();
});