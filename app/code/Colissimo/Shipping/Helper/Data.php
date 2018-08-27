<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Directory\Model\RegionFactory;

/**
 * Class Data
 */
class Data extends AbstractHelper
{

    /**
     * @var RegionFactory $regionFactory
     */
    protected $regionFactory;

    /**
     * @param Context $context
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        Context $context,
        RegionFactory $regionFactory
    ) {
        $this->regionFactory = $regionFactory;
        parent::__construct($context);
    }

    /**
     * Retrieve configuration value
     *
     * @param string $path
     * @return array|string|bool
     */
    public function getConfig($path)
    {
        $config = [
            'homecl' => [
                'product_code' => 'DOM',
                'country' => [
                    'FR' => [
                        'max_weight' => 30000,
                    ],
                    'BE' => [
                        'max_weight' => 30000,
                    ],
                    'CH' => [
                        'max_weight' => 30000,
                    ],
                ],
            ],
            'homesi' => [
                'product_code' => 'DOS',
                'country' => [
                    'FR' => [
                        'max_weight' => 30000,
                    ],
                    'BE' => [
                        'max_weight' => 30000,
                    ],
                    'NL' => [
                        'max_weight' => 30000,
                    ],
                    'DE' => [
                        'max_weight' => 30000,
                    ],
                    'GB' => [
                        'max_weight' => 30000,
                    ],
                    'LU' => [
                        'max_weight' => 30000,
                    ],
                    'ES' => [
                        'max_weight' => 30000,
                    ],
                    'AT' => [
                        'max_weight' => 30000,
                    ],
                    'EE' => [
                        'max_weight' => 30000,
                    ],
                    'HU' => [
                        'max_weight' => 30000,
                    ],
                    'LV' => [
                        'max_weight' => 30000,
                    ],
                    'LT' => [
                        'max_weight' => 30000,
                    ],
                    'CZ' => [
                        'max_weight' => 30000,
                    ],
                    'SK' => [
                        'max_weight' => 30000,
                    ],
                    'SI' => [
                        'max_weight' => 30000,
                    ],
                    'CH' => [
                        'max_weight' => 30000,
                    ],
                    'PT' => [
                        'max_weight' => 30000,
                    ],
                ],
            ],
            'domtomcl' => [
                'product_code' => 'COM',
                'country' => '::_domtomCountries',
            ],
            'domtomsi' => [
                'product_code' => 'CDS',
                'country' => '::_domtomCountries',
            ],
            'international' => [
                'product_code' => 'COLI',
                'country' => '::_internationalCountries',
            ],
            'pickup' => [
                'country' => [
                    'FR' => [
                        'option_inter' => '0',
                        'max_weight'   => 30000,
                    ],
                    'BE' => [
                        'option_inter' => '2',
                        'max_weight'   => 20000,
                    ],
                    'NL' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'DE' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'GB' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'LU' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'ES' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'PT' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'AT' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'LT' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'LV' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                    'EE' => [
                        'option_inter' => '1',
                        'max_weight'   => 20000,
                    ],
                ],
            ],
        ];

        $keys = explode('/', $path);

        $skip = false;
        foreach ($keys as $i => $key) {
            if ($skip) {
                $skip = false;
                continue;
            }
            if (isset($config[$key])) {
                $config = $config[$key];
                if (is_string($config)) {
                    if (preg_match('/^::/', $config)) {
                        $method = preg_replace('/^::/', '', $config);
                        $config = $this->$method();
                        $skip = true;
                    }
                }
            } else {
                $config = false;
                break;
            }
        }

        return $config;
    }

    /**
     * Retrieve International country
     *
     * @return array
     */
    protected function _internationalCountries()
    {
        return [
            'max_weight' => 30000,
        ];
    }

    /**
     * Retrieve Dom-Tom country
     *
     * @return array
     */
    protected function _domtomCountries()
    {
        return [
            'max_weight' => 30000,
        ];
    }

    /**
     * Retrieve country
     *
     * @param string $countryId
     * @param string $postcode
     * @return string
     */
    public function getCountry($countryId, $postcode = null)
    {
        if ($countryId == 'MC') { // Monaco
            $countryId = 'FR';
        }

        if ($countryId == 'AD') { // Andorre
            $countryId = 'FR';
        }

        if ($postcode) {
            if ($countryId == 'FR') {
                $countryId = $this->getDomTomCountry($postcode);
            }
        }

        return $countryId;
    }

    /**
     * Retrieve Dom Tom Country with postcode
     *
     * @param string $postcode
     * @return string
     */
    public function getDomTomCountry($postcode)
    {
        $countryId = 'FR';
        $postcodes = $this->getDomTomPostcodes();

        $postcode = preg_replace('/\s+/', '', $postcode);
        foreach ($postcodes as $code => $regex) {
            if (preg_match($regex, $postcode)) {
                $countryId = $code;
                break;
            }
        }
        return $countryId;
    }

    /**
     * Retrieve Dom-Tom countries code ISO-2
     *
     * @return array
     */
    public function getDomTomCountries()
    {
        return [
            'GP', // Guadeloupe
            'MQ', // Martinique
            'GF', // Guyane
            'RE', // La réunion
            'PM', // St-Pierre-et-Miquelon
            'YT', // Mayotte
            'TF', // Terres-Australes
            'WF', // Wallis-et-Futuna
            'PF', // Polynésie Française
            'NC', // Nouvelle-Calédonie
            'BL', // Saint-Barthélemy
            'MF', // Saint-Martin (partie française)
        ];
    }

    /**
     * Retrieve Dom-Tom postcodes
     *
     * @return array
     */
    public function getDomTomPostcodes()
    {
        return [
            'BL' => '/^97133$/', // Saint-Barthélemy
            'MF' => '/^97150$/', // Saint-Martin (partie française)
            'GP' => '/^971[0-9]{2}$/', // Guadeloupe
            'MQ' => '/^972[0-9]{2}$/', // Martinique
            'GF' => '/^973[0-9]{2}$/', // Guyane
            'RE' => '/^974[0-9]{2}$/', // La réunion
            'PM' => '/^975[0-9]{2}$/', // St-Pierre-et-Miquelon
            'YT' => '/^976[0-9]{2}$/', // Mayotte
            'TF' => '/^984[0-9]{2}$/', // Terres-Australes
            'WF' => '/^986[0-9]{2}$/', // Wallis-et-Futuna
            'PF' => '/^987[0-9]{2}$/', // Polynésie Française
            'NC' => '/^988[0-9]{2}$/', // Nouvelle-Calédonie
        ];
    }

    /**
     * Retrieve region
     *
     * @param string $countryId
     * @param string $postcode
     * @return \Magento\Directory\Model\Region
     */
    public function getRegion($countryId, $postcode)
    {
        $code = (int)substr($postcode, 0, 2);
        if ($code == 20) {
            $code = (int)$postcode >= 20200 ? '2B' : '2A';
        }
        if ($this->getDomTomCountry($postcode) != 'FR') {
            $countryId = 'FR';
            $code = 'OM';
        }
        $instance = $this->regionFactory->create();

        return $instance->loadByCode($code, $countryId);
    }

    /**
     * Retrieve API configuration
     *
     * @return array
     */
    public function getApiConfig()
    {
        return [
            'wsdl'     => 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl',
            'login'    => $this->scopeConfig->getValue('carriers/colissimo/pickup/account_number'),
            'password' => $this->scopeConfig->getValue('carriers/colissimo/pickup/account_password'),
        ];
    }

    /**
     * Retrieve weight unit
     *
     * @return string
     */
    public function getWeightUnit()
    {
        return $this->scopeConfig->getValue('general/locale/weight_unit');
    }

    /**
     * Convert weight in gram to config weight unit
     *
     * @param float $weight
     * @param string $unit
     * @return float
     */
    public function convertWeightFromGramToStoreUnit($weight, $unit = null)
    {
        if (!$unit) {
            $unit = $this->getWeightUnit();
        }

        if ($unit == 'kgs' || $unit == 'KILOGRAM') {
            $weight = $weight * 0.001;
        }

        if ($unit == 'lbs' || $unit == 'POUND') {
            $weight = $weight * 0.00220462;
        }

        return $weight;
    }

    /**
     * Convert weight in gram to config weight unit
     *
     * @param float $weight
     * @param string $unit
     * @return float
     */
    public function convertWeightFromStoreUnitToGram($weight, $unit = null)
    {
        if (!$unit) {
            $unit = $this->getWeightUnit();
        }

        if ($unit == 'kgs' || $unit == 'KILOGRAM') {
            $weight = $weight / 0.001;
        }

        if ($unit == 'lbs' || $unit == 'POUND') {
            $weight = $weight / 0.00220462;
        }

        return $weight;
    }
}