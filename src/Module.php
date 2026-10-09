<?php 
declare(strict_types=1);
namespace OSW3\WpFeatAnalytics;

use OSW3\WpFeatAnalytics\Providers\GoogleAnalytics4Provider;
use OSW3\WpFeatAnalytics\Providers\PlausibleProvider;
use OSW3\WpFeatAnalytics\Services\AnalyticsService;
use OSW3\WpFeatAnalytics\Services\ConfigService;
use OSW3\WpSiteCore\Interfaces\ModuleInterface;

final class Module implements ModuleInterface
{
    /**
     * Dependencies required by the module, defined as an associative array where 
     * the key is the plugin file path and the value is the readable name of the 
     * plugin.
     * 
     * e.g., 'wp-site-core/wp-site-core.php' => 'WP Site Core'
     * 
     * @var array<string, string>
     */
    public const DEPENDENCIES = [
    ];

    /**
     * Singleton instance of the Module class.
     */
    private static ?self $instance = null;

    /**
     * Singleton instance of the AnalyticsService.
     */
    private static ?AnalyticsService $service = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        $this->boot();
    }

    /**
     * Get the singleton instance of the Module class.
     *
     * @return self The singleton instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the unique identifier of the module.
     * 
     * @return string
     */
    public static function getId(): string
    {
        return 'wp-feat-analytics';
    }

    /**
     * Get the text domain of the module.
     *
     * @return string The text domain of the module.
     */
    public static function getDomain(): string
    {
        return 'wp-feat-analytics';
    }

    /**
     * Get the name of the module.
     *
     * @return string The name of the module.
     */
    public static function getName(): string
    {
        return 'WP Feature : Analytics';
    }

    /**
     * Get the description of the module.
     *
     * @return string The description of the module.
     */
    public static function getDescription(): string
    {
        return 'Starter module for WP Feature Analytics.';
    }

    /**
     * Get the version of the module.
     *
     * @return string The version of the module.
     */
    public static function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Get the main file of the module.
     *
     * @return string The main file of the module.
     */
    public static function getFile(): string
    {
        return 'wp-feat-analytics/wp-feat-analytics.php';
    }

    /**
     * Get the path of the module.
     *
     * @return string The path of the module.
     */
    public static function getPath(): string
    {
        $path = plugin_dir_path(__FILE__);
        $path = str_replace('src/', '', $path);
        return $path;
    }

    /**
     * Initialize the Module class.
     * 
     * @return void
     */
    public static function init(): void
    {
        self::getInstance();
    }
    
    /**
     * Boot the module by initializing services and preventing deactivation if needed.
     * 
     * @return void
     */
    public function boot(): void
    {
        self::$service = new AnalyticsService();
        
        // Chargement de GA4 si la clé existe
        $ga4Id = ConfigService::get('ANALYTICS_GA4_ID');
        if ($ga4Id !== null) {
            self::$service->addProvider(new GoogleAnalytics4Provider($ga4Id));
        }

        // Chargement de Plausible si la clé existe
        $plausibleDomain = ConfigService::get('ANALYTICS_PLAUSIBLE_DOMAIN');
        if ($plausibleDomain !== null) {
            self::$service->addProvider(new PlausibleProvider($plausibleDomain));
        }

        add_action('wp_head', [self::$service, 'injectHeaderScripts'], 1);
        add_action('wp_footer', [self::$service, 'injectFooterScripts'], 99);
    }
}