<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Block\Adminhtml\System\Config\Form\Field\Price;

use Colissimo\Shipping\Block\Adminhtml\System\Config\Form\Field\Price;

/**
 * Class Pickup
 */
class Pickup extends Price
{

    /**
     * Countries setter
     */
    public function setCountries()
    {
        $active = $this->shippingHelper->getConfig('pickup/country');

        $this->countries = array_keys($active);
    }
}
