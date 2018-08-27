# Project Name

Ingenico ePayments extension for Ogone platform

## Description

Integrates the Ingenico ePayments Ogone platform into Magento, enabling multiple payment methods and supporting the Magento business workflow.

## Requirements

* PHP >= 5.6.0
* Magento version as specified in composer.json of this project
* Ingenico ePayments account ([Account registration](https://payment-services.ingenico.com/int/en/free-test-account))

## Installation

1. Upload the package archive to your webserver. Do not unpack it. The path must be accessible for Composer.
2. Go to your Magento2 root folder in a SSH session
3. Enter the following commands:
    ```
    composer config repositories.ingenico2 artifact "/path/to/folder/with/uploaded/archive"
    composer require ingenico/ingenico_epayments_ogn2
    ```
    Wait while dependencies are updated.
4. Enter the following commands to enable the module:
    ```
    php bin/magento module:enable Netresearch_OPS --clear-static-content
    php bin/magento setup:upgrade
    rm ­rf var/di
    rm ­rf var/generation/*
    php bin/magento cache:clean
    php bin/magento setup:di:compile
    php bin/magento setup:static-content deploy <list_of_your_locales>
    ```
5. Enable and configure the extension in the Magento backend under `Stores > Configuration > Payment Services` and `Stores > Configuration > Payment Methods`

## Support

For receiving support regarding account and platform issues contact [Ingenicos support](mailto:support.ecom@ingenico.com)
For bugs, extension issues, or feature requests contact [Netresearchs technical support](mailto:ingenico.support@netresearch.de).

## Credits

See contributors

## License

[OSL 3.0 License](https://opensource.org/licenses/OSL-3.0)
