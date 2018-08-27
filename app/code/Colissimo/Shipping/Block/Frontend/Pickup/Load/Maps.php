<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Block\Frontend\Pickup\Load;

use Magento\Framework\View\Element\Template;

/**
 * Class Maps
 */
class Maps extends Template
{

    /**
     * Retrieve Maps API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_scopeConfig->getValue('carriers/colissimo/pickup/api_key');
    }
}