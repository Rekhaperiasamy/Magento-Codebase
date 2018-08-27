# Installation

## With composer

1. Update Magento **composer.json** as follows:

```json
{
    ...
    "require": {
        ...
        "colissimo/module-shipping": "@stable"
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
        "colissimo/module-shipping": {
            "type": "vcs",
            "url": "https://github.com/magentix/colissimo-shipping.git"
        }
    },
    ...
}
```

_Generate Github **Personal access token** from your account (Settings > Personal access tokens)._

2. Add package:

```shell
composer require colissimo/module-shipping
```

## By download

1. Download the latest release from module repository

2. Create **app/code/Colissimo/Shipping** directory in Magento

3. Unzip module archive content in **app/code/Colissimo/Shipping** directory

## Enable Module

Enable and install module in Magento:

```shell
php bin/magento module:enable Colissimo_Shipping
php bin/magento setup:db:status
php bin/magento setup:upgrade
php bin/magento cache:flush
php bin/magento setup:di:compile
```

# Contact

support@magentix.fr