<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model;

use Colissimo\Shipping\Helper\Data as ShippingHelper;
use Magento\Framework\DataObject;

/**
 * Class Pickup
 */
class Address extends DataObject
{
    /**
     * @var ShippingHelper $shippingHelper
     */
    protected $shippingHelper;

    /**
     * @var Pickup $pickup
     */
    protected $pickup;

    /**
     * @param ShippingHelper $shippingHelper
     * @param Pickup $pickup
     * @param array $data
     */
    public function __construct(
        ShippingHelper $shippingHelper,
        Pickup $pickup,
        array $data = []
    ) {
        $this->pickup = $pickup;
        $this->shippingHelper = $shippingHelper;
        parent::__construct($data);
    }

    /**
     * Update Shipping Address
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function updateShippingAddress($order, $quote)
    {
        if (!$order) {
            return $this;
        }

        if (!$quote) {
            return $this;
        }

        $address = $order->getShippingAddress();

        if ($address->getAddressType() !== 'shipping') {
            return $this;
        }

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();

        if (!$shippingMethod) {
            return $this;
        }

        list($code, $method) = explode('_', $shippingMethod);

        /* Reset Data */
        $address->setColissimoProductCode(null);
        $address->setColissimoPickupId(null);
        $address->setColissimoNetworkCode(null);

        if ($code !== 'colissimo') {
            return $this;
        }

        /* Set code */
        $productCode = $this->shippingHelper->getConfig($method . '/product_code');
        if ($productCode) {
            $address->setColissimoProductCode($productCode);
        }

        /* Set pickup data */
        $pickup = $this->pickup->current($quote->getId());

        if ($pickup->hasData()) {
            $address->setCompany($pickup->getNom())
                ->setStreet([$pickup->getData('adresse1') . "\n" . $pickup->getData('adresse2')])
                ->setPostcode($pickup->getCodePostal())
                ->setCity($pickup->getLocalite())
                ->setCountryId($pickup->getCodePays())
                ->setFax('')
                ->setCustomerAddressId(null)
                ->setColissimoPickupId($pickup->getIdentifiant())
                ->setColissimoProductCode($pickup->getTypeDePoint())
                ->setColissimoNetworkCode($pickup->getReseau())
                ->setSameAsBilling(0)
                ->setSaveInAddressBook(0);

            $region = $this->shippingHelper->getRegion($pickup->getCodePays(), $pickup->getCodePostal());
            if ($region->hasData()) {
                $address->setRegion($region->getDefaultName())
                    ->setRegionId($region->getRegionId())
                    ->setRegionCode($region->getCode());
            }

            $this->pickup->reset($quote->getId());
        }

        return $this;
    }
}