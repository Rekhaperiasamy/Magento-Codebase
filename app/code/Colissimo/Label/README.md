# Installation

## With composer

1. Update Magento **composer.json** as follows:

```json
{
    ...
    "require": {
        ...
        "colissimo/module-label": "@stable"
    },
    ...
    "config": {
        ...
        "github-oauth": {
            "github.com": "123456789123456789123456789123456789"
        }
    },
    ...
    "repositories": {
        ...
        "colissimo/module-label": {
            "type": "vcs",
            "url": "https://github.com/magentix/colissimo-label.git"
        }
    },
    ...
}
```

_Generate Github **Personal access token** from your account (Settings > Personal access tokens)._

2. Add package:

```shell
composer require colissimo/module-label
```

## By download

1. Download the latest release from module repository

2. Create **app/code/Colissimo/Label** directory in Magento

3. Unzip module archive content in **app/code/Colissimo/Label** directory

## Enable Module

Enable and install module in Magento:

```shell
php bin/magento module:enable Colissimo_Label
php bin/magento setup:db:status
php bin/magento setup:upgrade
php bin/magento cache:flush
php bin/magento setup:di:compile
```

# Contact

support@magentix.fr