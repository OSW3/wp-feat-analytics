<?php
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics\Support;

use OSW3\WpFeatAnalytics\Module;
use OSW3\WpFeatAnalytics\Guards\EnvironmentGuard;
use OSW3\WpFeatAnalytics\Guards\DependenciesGuard;

final class Activator 
{
    public static function activate(): void
    {
        flush_rewrite_rules();
        
        if (get_option(Module::getId(), false)) {
            return;
        }
        
        $issues = array_merge(
            EnvironmentGuard::check(),
            DependenciesGuard::check()
        );

        if ($issues !== []) {
            // Deactivate the plugin if there are compatibility issues
            deactivate_plugins(plugin_basename(Module::getFile()));

            // Prepare the title for the error message
            $title = esc_html(sprintf(__('Activation failed - %s', Module::getDomain()), Module::getName()));

            // Display an error message to the user
            $message = sprintf(
                '<h3>%s</h3><ul><li>%s</li></ul>',
                esc_html(sprintf(__('Impossible d\'activer %s. Les prérequis suivants ne sont pas respectés :', Module::getDomain()), Module::getName())),
                implode('</li><li>', array_map('esc_html', $issues))
            );
            
            // Use wp_die to display the error message and provide a back link
            wp_die($message, $title, ['back_link' => true]);
        }

        // If there are no issues, proceed with activation tasks
        // For example, you can initialize default settings or create necessary database tables here.
        // ...
        
        
        // ...

        
        // Mark the module as activated in the database.
        update_option(Module::getId(), true);
    }
}