<?php
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics\Services;

use OSW3\WpFeatAnalytics\Interfaces\ProviderInterface;

final class AnalyticsService
{
    /** @var ProviderInterface[] */
    private array $providers = [];

    /**
     * Add a provider to the analytics service.
     *
     * @param ProviderInterface $provider The analytics provider to add.
     * @return self
     */
    public function addProvider(ProviderInterface $provider): self
    {
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * Inject the header scripts for all enabled analytics providers.
     *
     * @return void
     */ 
    public function injectHeaderScripts(): void
    {
        // Désactiver pour les utilisateurs connectés / admins
        if (current_user_can('manage_options')) {
            // return;
        }

        foreach ($this->providers as $provider) {
            if ($provider->isEnabled()) {
                echo $provider->renderHeaderScript() . "\n";
            }
        }
    }
    
    /**
     * Inject the footer scripts for all enabled analytics providers.
     *
     * @return void
     */
    public function injectFooterScripts(): void
    {
        if (current_user_can('manage_options')) {
            return;
        }

        foreach ($this->providers as $provider) {
            if ($provider->isEnabled()) {
                echo $provider->renderFooterScript() . "\n";
            }
        }
    }
}