<?php
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics\Enums;

enum Provider: string
{
    case GA4 = 'ga4';
    case MATOMO = 'matomo';
    case PLAUSIBLE = 'plausible';
}