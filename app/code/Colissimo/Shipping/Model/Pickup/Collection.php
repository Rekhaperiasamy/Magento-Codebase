<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model\Pickup;

use Colissimo\Shipping\Model\Soap;
use Colissimo\Shipping\Model\Pickup;
use Colissimo\Shipping\Helper\Data as ShippingHelper;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection as DataCollection;

/**
 * Class Collection
 */
class Collection extends DataCollection
{

    /**
     * @var ShippingHelper $shippingHelper
     */
    protected $shippingHelper;

    /**
     * @var Soap $soap
     */
    protected $soap;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param ShippingHelper $shippingHelper
     * @param Soap $soap
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ShippingHelper $shippingHelper,
        Soap $soap
    ) {
        parent::__construct($entityFactory);
        $this->shippingHelper = $shippingHelper;
        $this->soap = $soap;
    }

    /**
     * Retrieve collection items
     *
     * @return \Colissimo\Shipping\Model\Pickup[]
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Retrieve collection all items count
     *
     * @return int
     */
    public function getSize()
    {
        return count($this->getItems());
    }

    /**
     * Retrieve Pickup list
     *
     * @param Pickup $object
     * @param string $street
     * @param string $city
     * @param string $postcode
     * @param string $country
     * @return $this
     */
    public function loadItems($object, $street, $city, $postcode, $country)
    {
        $optionInter = $this->shippingHelper->getConfig('pickup/country/' . $country . '/option_inter');

        $data = [
            'address'       => $street,
            'zipCode'       => $postcode,
            'city'          => $city,
            'countryCode'   => $country,
            'shippingDate'  => date('d/m/Y'),
            'filterRelay'   => '1',
            'requestId'     => md5(rand(0, 99999)),
            'optionInter'   => $optionInter ?: 0,
        ];

        $response = $this->soap->execute('findRDVPointRetraitAcheminement', $data);

        if ($response['error']) {
            return $this;
        }

        if (isset($response['response']->listePointRetraitAcheminement)) {
            foreach ($response['response']->listePointRetraitAcheminement as $data) {
                $item = clone $object;
                foreach ($data as $k => $v) {
                    $key = preg_replace_callback(
                        '/([A-Z])/', create_function('$m', 'return "_".strtolower($m[1]);'), $k
                    );
                    $item->setData(trim($key, '_'), $v);
                }
                $this->addItem($item);
            }
        }

        return $this;
    }
}