[![version](https://img.shields.io/badge/version-1.1.0-green.svg)](https://github.com/steevanb/doctrine-stats/tree/1.1.0)
[![doctrine](https://img.shields.io/badge/doctrine/orm-^2.4-blue.svg)](http://www.doctrine-project.org)
[![php](https://img.shields.io/badge/php-^5.4.6 || ^7.0-blue.svg)](http://www.php.net)
![Lines](https://img.shields.io/badge/code lines-1728-green.svg)
![Total Downloads](https://poser.pugx.org/steevanb/doctrine-stats/downloads)
[![SensionLabsInsight](https://img.shields.io/badge/SensionLabsInsight-platinum-brightgreen.svg)](https://insight.sensiolabs.com/projects/884a7b62-bb7a-41dc-8198-6d2bb0694795/analyses/7)
[![Scrutinizer](https://scrutinizer-ci.com/g/steevanb/doctrine-stats/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/steevanb/doctrine-stats/)

doctrine-stats
--------------

Add important Doctrine statistics :
* count managed entities
* count lazy loaded entities
* hydration time by hydrator
* group queries by query string, show differents parameters used by same query string
* count different query string used

[Changelog](changelog.md)

Installation
------------

```json
# composer.json
{
    "require-dev": {
        "steevanb/doctrine-stats": "^1.1"
    }
}
```

If you want to add hydration time to your statistics :

```json
# composer.json
{
    "require-dev": {
        "steevanb/composer-overlad-class": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "ComposerOverloadClass\\": "var/cache/ComposerOverloadClass"
        }
    },
    "scripts": {
        "pre-autoload-dump": "steevanb\\ComposerOverloadClass\\OverloadClass::overload"
    },
    "extra": {
        "composer-overload-cache-dir": "var/cache",
        "composer-overload-class-dev": {
            "Doctrine\\ORM\\Internal\\Hydration\\ArrayHydrator": {
                "original-file": "vendor/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/ArrayHydrator.php",
                "overload-file": "vendor/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/ArrayHydrator.php"
            },
            "Doctrine\\ORM\\Internal\\Hydration\\ObjectHydrator": {
                "original-file": "vendor/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/ObjectHydrator.php",
                "overload-file": "vendor/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/ObjectHydrator.php"
            },
            "Doctrine\\ORM\\Internal\\Hydration\\ScalarHydrator": {
                "original-file": "vendor/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/ScalarHydrator.php",
                "overload-file": "vendor/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/ScalarHydrator.php"
            },
            "Doctrine\\ORM\\Internal\\Hydration\\SimpleObjectHydrator": {
                "original-file": "vendor/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/SimpleObjectHydrator.php",
                "overload-file": "vendor/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/SimpleObjectHydrator.php"
            },
            "Doctrine\\ORM\\Internal\\Hydration\\SingleScalarHydrator": {
                "original-file": "vendor/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/SingleScalarHydrator.php",
                "overload-file": "vendor/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/SingleScalarHydrator.php"
            }
        }
    }
}
```

Symfony 2.x and 3.x integration
-------------------------------

Read Installation paragraph before.

```php
# app/AppKernel.php
class AppKernel
{
    public function registerBundles()
    {
        if ($this->getEnvironment() === 'dev') {
            $bundles[] = new \steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DoctrineStatsBundle();
        }
    }
}
```

If you want to add lazy loaded entities to your statistics :

```yml
# app/config/config_dev.yml
parameters:
    doctrine.orm.entity_manager.class: steevanb\DoctrineStats\Doctrine\ORM\EntityManager
```

Manual integration 
------------------

To retrieve statistics, you need to register steevanb\DoctrineStats\EventSubscriber\DoctrineEventSubscriber in your event manager.

If you want to add lazy loaded entities to your statistics, you need to overload default EntityManager, with steevanb\DoctrineStats\Doctrine\ORM\EntityManager.
