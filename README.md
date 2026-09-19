# WP Feat Analytics (`wp-feat-analytics`)

Un module WordPress moderne, fortement typé (PHP 8+, strict types) et modulaire pour intégrer facilement des services d'analyse d'audience (Google Analytics 4, Plausible Analytics, etc.) sur votre site WordPress.

---

## 📋 Prérequis

- **PHP** : `>= 8.1`
- **WordPress** : `>= 6.0`
- **Composer** (pour l'autoloading des classes)

---

## 🚀 Installation

### Via Composer (Recommandé sous Bedrock / Roots)

Ajoutez le module à votre projet WordPress :

```bash
composer require osw3/wp-feat-analytics

```

### Via installation manuelle

Placez le dossier `wp-feat-analytics` directement dans le répertoire `wp-content/plugins/` ou `wp-content/mu-plugins/` de votre site WordPress, puis exécutez `composer install` à la racine du module.

---

## ⚙️ Configuration

`wp-feat-analytics` détecte automatiquement la configuration à partir de trois sources différentes, par ordre de priorité :

1. **Variables d'environnement (`.env`)**
2. **Constantes PHP (`wp-config.php`)**
3. **Options WordPress (`wp_options`)**

### 1. Via le fichier `.env` (Recommandé)

Ajoutez les identifiants de vos services dans votre fichier `.env` à la racine du projet :

```ini
# Google Analytics 4
ANALYTICS_GA4_ID="G-XXXXXXXXXX"

# Plausible Analytics
ANALYTICS_PLAUSIBLE_DOMAIN="mon-site.fr"

```

### 2. Via `wp-config.php`

Si vous n'utilisez pas de fichier `.env`, vous pouvez définir les constantes directement dans `wp-config.php` :

```php
define('ANALYTICS_GA4_ID', 'G-XXXXXXXXXX');
define('ANALYTICS_PLAUSIBLE_DOMAIN', 'mon-site.fr');

```

### 3. Via les options WordPress / WP-CLI

Vous pouvez enregistrer les identifiants en base de données via WP-CLI :

```bash
wp option update wp_feat_analytics_ga4_id "G-XXXXXXXXXX"
wp option update wp_feat_analytics_plausible_domain "mon-site.fr"

```

---

## 🛠️ Utilisation

Le module s'initialise automatiquement au chargement des plugins WordPress via le fichier principal `wp-feat-analytics.php`.

Si vous souhaitez l'initialiser manuellement dans un thème ou un autre plugin :

```php
use OSW3\WpFeatAnalytics\Module;

add_action('plugins_loaded', function () {
    Module::init();
});

```

### Fonctionnement du rendu

* **Injection automatique** : Les scripts des providers configurés sont injectés dans le `<head>` (`wp_head`) ou le `<footer>` (`wp_footer`).
* **Exclusion des administrateurs** : Les utilisateurs connectés ayant la capacité `manage_options` (administrateurs) ne sont **pas** suivis afin d'éviter de polluer vos métriques d'audience.

---

## 🏗️ Architecture & Structure du projet

Le module respecte une architecture orientée objet et extensible à 7 fichiers principaux :

```text
wp-feat-analytics/
├── src/
│   ├── Enums/
│   │   └── Provider.php                # Enum des providers supportés
│   ├── Interfaces/
│   │   └── ProviderInterface.php       # Contrat pour la création de providers
│   ├── Providers/
│   │   ├── GoogleAnalytics4Provider.php # Provider Google Analytics 4
│   │   └── PlausibleProvider.php       # Provider Plausible Analytics
│   ├── Services/
│   │   ├── ConfigService.php           # Service de résolution de la configuration
│   │   └── AnalyticsService.php        # Service d'injection des scripts
│   └── Module.php                      # Point d'entrée principal du module
├── wp-feat-analytics.php               # Fichier d'entête du plugin WordPress
├── composer.json                       # Fichier de dépendances et d'autoload
└── README.md                           # Documentation du module

```

---

## ➕ Ajouter un nouveau Provider

Pour ajouter un nouveau service d'analyse d'audience (ex: Matomo, Fathom, etc.) :

1. Créez une nouvelle classe dans `src/Providers/` implémentant `ProviderInterface` :
```php
<?php

declare(strict_types=1);

namespace OSW3\WpFeatAnalytics\Providers;

use OSW3\WpFeatAnalytics\Interfaces\ProviderInterface;

final class CustomProvider implements ProviderInterface
{
    public function __construct(private readonly ?string $siteId) {}

    public function isEnabled(): bool
    {
        return !empty($this->siteId);
    }

    public function renderHeaderScript(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return '<script src="[https://example.com/script.js](https://example.com/script.js)" data-site="' . esc_attr($this->siteId) . '"></script>';
    }

    public function renderFooterScript(): string
    {
        return '';
    }
}

```


2. Déclarez le chargement du provider dans `src/Module.php` :
```php
$customId = ConfigService::get('ANALYTICS_CUSTOM_ID');
if ($customId !== null) {
    self::$service->addProvider(new CustomProvider($customId));
}

```



---

## 📜 Licence

Ce projet est sous licence MIT.
