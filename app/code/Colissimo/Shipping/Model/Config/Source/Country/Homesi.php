<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model\Config\Source\Country;

use Colissimo\Shipping\Model\Config\Source\Country;

/**
 * Class Homesi
 */
class Homesi extends Country
{

    /**
     * Countries setter
     */
    public function setCountries()
    {
        $active = $this->shippingHelper->getConfig('homesi/country');

        $this->countries = array_keys($active);
    }
}