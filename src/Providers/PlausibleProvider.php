<?php
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics\Providers;

use OSW3\WpFeatAnalytics\Interfaces\ProviderInterface;

final class PlausibleProvider implements ProviderInterface
{
    /**
     * PlausibleProvider constructor.
     *
     * @param string $domain The domain for Plausible Analytics.
     * @param string $src The source URL for the Plausible script.
     */
    public function __construct(
        private readonly string $domain,
        private readonly string $src = 'https://plausible.io/js/script.js'
    ) {}

    /**
     * Check if the Plausible provider is enabled.
     *
     * @return bool True if the provider is enabled, false otherwise.
     */
    public function isEnabled(): bool
    {
        return !empty($this->domain);
    }

    /**
     * Render the header script for the Plausible provider.
     *
     * @return string The header script.
     */
    public function renderHeaderScript(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $domain = esc_attr($this->domain);
        $src = esc_url($this->src);

        return <<<HTML
<!-- Plausible Analytics -->
<script defer data-domain="{$domain}" src="{$src}"></script>
HTML;
    }

    /**
     * Render the footer script for the Plausible provider.
     *
     * @return string The footer script.
     */
    public function renderFooterScript(): string
    {
        return '';
    }
}