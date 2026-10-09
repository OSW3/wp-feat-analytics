<?php
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics\Support;

use OSW3\WpFeatAnalytics\Module;

final class Deactivator
{
    public static function deactivate(): void
    {
        flush_rewrite_rules();
        
        // Mark the module as deactivated in the database.
        update_option(Module::getId(), false);
    }
}
