<?php
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics\Interfaces;

interface ProviderInterface
{
    /**
     * Check if the provider is enabled.
     *
     * @return bool True if the provider is enabled, false otherwise.
     */
    public function isEnabled(): bool;

    /**
     * Render the header script for the provider.
     *
     * @return string The header script.
     */
    public function renderHeaderScript(): string;

    /**
     * Render the footer script for the provider.
     *
     * @return string The footer script.
     */
    public function renderFooterScript(): string;
}