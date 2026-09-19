<?php
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics\Providers;

use OSW3\WpFeatAnalytics\Interfaces\ProviderInterface;

final class GoogleAnalytics4Provider implements ProviderInterface
{
    /**
     * GoogleAnalytics4Provider constructor.
     *
     * @param string $measurementId The measurement ID for Google Analytics 4.
     * @param bool $anonymizeIp Whether to anonymize IP addresses.
     */
    public function __construct(
        private readonly string $measurementId,
        private readonly bool $anonymizeIp = true
    ) {}

    /**
     * Check if the Google Analytics 4 provider is enabled.
     *
     * @return bool True if the provider is enabled, false otherwise.
     */
    public function isEnabled(): bool
    {
        return !empty($this->measurementId);
    }

    /**
     * Render the header script for the Google Analytics 4 provider.
     *
     * @return string The header script.
     */
    public function renderHeaderScript(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $id = esc_attr($this->measurementId);

        return <<<HTML
<!-- Google Tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$id}');
</script>
HTML;
    }

    /**
     * Render the footer script for the Google Analytics 4 provider.
     *
     * @return string The footer script.
     */
    public function renderFooterScript(): string
    {
        return '';
    }
}