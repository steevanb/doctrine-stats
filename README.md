[![version](https://img.shields.io/badge/version-1.0.0-green.svg)](https://github.com/steevanb/doctrine-stats/tree/1.0.0)
[![doctrine](https://img.shields.io/badge/doctrine/orm-^2.4-blue.svg)](http://www.doctrine-project.org)
[![php](https://img.shields.io/badge/php->= 5.4-blue.svg)](http://www.doctrine-project.org)
![Lines](https://img.shields.io/badge/code lines-1377-green.svg)
![Total Downloads](https://poser.pugx.org/steevanb/doctrine-stats/downloads)
[![SensionLabsInsight](https://img.shields.io/badge/SensionLabsInsight-platinum-brightgreen.svg)](https://insight.sensiolabs.com/projects/884a7b62-bb7a-41dc-8198-6d2bb0694795/analyses/1)
[![Scrutinizer](https://scrutinizer-ci.com/g/steevanb/doctrine-stats/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/steevanb/doctrine-stats/)

[![version](https://img.shields.io/badge/status-dev-red.svg)](https://github.com/steevanb/doctrine-stats)
[![doctrine](https://img.shields.io/badge/doctrine/orm-^2.4.8-blue.svg)](http://www.doctrine-project.org)

# doctrine-stats

Installation
------------

```json
# composer.json
{
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
        "composer-overload-class": {
            "Doctrine\\ORM\\Internal\\Hydration\\ArrayHydrator": {
                "original-file": "vendor/composer/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/ArrayHydrator.php",
                "overload-file": "vendor/composer/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/ArrayHydrator.php"
            },
            "Doctrine\\ORM\\Internal\\Hydration\\ObjectHydrator": {
                "original-file": "vendor/composer/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/ObjectHydrator.php",
                "overload-file": "vendor/composer/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/ObjectHydrator.php"
            },
            "Doctrine\\ORM\\Internal\\Hydration\\ScalarHydrator": {
                "original-file": "vendor/composer/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/ScalarHydrator.php",
                "overload-file": "vendor/composer/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/ScalarHydrator.php"
            },
            "Doctrine\\ORM\\Internal\\Hydration\\SimpleObjectHydrator": {
                "original-file": "vendor/composer/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/SimpleObjectHydrator.php",
                "overload-file": "vendor/composer/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/SimpleObjectHydrator.php"
            },
            "Doctrine\\ORM\\Internal\\Hydration\\SingleScalarHydrator": {
                "original-file": "vendor/composer/doctrine/orm/lib/Doctrine/ORM/Internal/Hydration/SingleScalarHydrator.php",
                "overload-file": "vendor/composer/steevanb/doctrine-stats/ComposerOverloadClass/Doctrine/ORM/Internal/SingleScalarHydrator.php"
            }
        }
    }
}
```

lazy loaded entities only :
```yml
# app/config/config_dev.yml

parameters:
    doctrine.orm.entity_manager.class: steevanb\DoctrineStats\Doctrine\ORM\EntityManager
```
