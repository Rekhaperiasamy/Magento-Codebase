<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 */
class Data extends AbstractHelper
{

    /**
     * @var DirectoryList $directoryList
     */
    protected $directoryList;

    /**
     * @param DirectoryList $directoryList
     * @param Context $context
     */
    public function __construct(
        DirectoryList $directoryList,
        Context $context
    ) {
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * Retrieve Label Path
     *
     * @return string
     */
    public function getLabelPath()
    {
        $path = $this->scopeConfig->getValue('shipping/colissimo_label/label_path');

        if (substr($path, 0, 1) !== '/') {
            $path = $this->directoryList->getPath(DirectoryList::ROOT) . DIRECTORY_SEPARATOR . $path;
        }

        $path = rtrim($path, '/') . DIRECTORY_SEPARATOR;

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    /**
     * Retrieve weight unit
     *
     * @param string $code
     * @param string $method
     * @return string
     */
    public function getInsurance($code, $method)
    {
        if (empty($code) && empty($method)) {
            return false;
        }

        return intval($this->scopeConfig->getValue('carriers/' . $code . '/' . $method . '/insurance'));
    }

    /**
     * Retrieve is shipping has CN23
     *
     * @param string $countryCode
     * @param string $postcode
     * @return bool
     */
    public function isCn23($countryCode, $postcode)
    {
        $isCn23 = false;

        if ($countryCode !== 'FR') {
            $isCn23 = true;
        }

        if ($countryCode == 'FR' && $this->isDomTom($postcode)) {
            $isCn23 = true;
        }

        return $isCn23;
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
     * Retrieve Dom Tom Country with postcode
     *
     * @param string $postcode
     * @return bool
     */
    public function isDomTom($postcode)
    {
        $isDomTom = false;
        $postcodes = $this->getDomTomPostcodes();

        $postcode = preg_replace('/\s+/', '', $postcode);
        foreach ($postcodes as $code => $regex) {
            if (preg_match($regex, $postcode)) {
                $isDomTom = true;
                break;
            }
        }

        return $isDomTom;
    }

    /**
     * Retrieve Dom-Tom postcodes
     *
     * @return array
     */
    public function getDomTomPostcodes()
    {
        return array(
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
        );
    }

    /**
     * Retrieve Label size
     *
     * @return string
     */
    public function getLabelSize()
    {
        return $this->scopeConfig->getValue('shipping/colissimo_label/label_size');
    }

    /**
     * Retrieve Commercial Name
     *
     * @return string
     */
    public function getCommercialName()
    {
        return $this->scopeConfig->getValue('shipping/colissimo_label/commercial_name');
    }

    /**
     * Retrieve street lines number
     *
     * @return int
     */
    public function getStreetLines()
    {
        return $this->scopeConfig->getValue('customer/address/street_lines');
    }

    /**
     * Retrieve account number
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->scopeConfig->getValue('shipping/colissimo_label/account_number');
    }

    /**
     * Retrieve account password
     *
     * @return string
     */
    public function getAccountPassword()
    {
        return $this->scopeConfig->getValue('shipping/colissimo_label/account_password');
    }

    /**
     * Retrieve Site name
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->scopeConfig->getValue('shipping/colissimo_label/site_name');
    }

    /**
     * Retrieve Site name
     *
     * @return string
     */
    public function getSiteNumber()
    {
        return $this->scopeConfig->getValue('shipping/colissimo_label/site_number');
    }

    /**
     * Retrieve number of day for label deletion in database
     *
     * @return int
     */
    public function getDeleteLabelAfter()
    {
        return $this->scopeConfig->getValue('shipping/colissimo_label/delete_label_after');
    }

    /**
     * Update country code
     *
     * @param string $countryCode
     * @param string $postcode
     * @return string
     */
    public function getCountryCode($countryCode, $postcode)
    {
        if ($countryCode == 'FR') {
            if ($postcode == '98000') {
                $countryCode = 'MC';
            }
            if (preg_match('/^AD/', $postcode)) {
                $countryCode = 'AD';
            }
        }

        return $countryCode;
    }

    /**
     * Matches product codes for WS
     *
     * @param string $productCode
     * @return string
     */
    public function getProductCode($productCode)
    {
        $matches = array(
            'ACP'  => 'BPR',
            'CDI'  => 'BPR',
            'COLD' => 'DOM',
            'COL'  => 'DOS',
        );

        if (isset($matches[$productCode])) {
            return $matches[$productCode];
        }

        return $productCode;
    }

    /**
     * Retrieve API configuration
     *
     * @return array
     */
    public function getApiConfig()
    {
        return [
            'wsdl'     => 'https://ws.colissimo.fr/sls-ws/SlsServiceWS?wsdl',
            'login'    => $this->getAccountNumber(),
            'password' => $this->getAccountPassword(),
        ];
    }
}