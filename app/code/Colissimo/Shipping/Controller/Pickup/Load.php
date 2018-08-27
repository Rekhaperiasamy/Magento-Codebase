<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Controller\Pickup;

use Colissimo\Shipping\Helper\Data as ShippingHelper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Load
 */
class Load extends Action
{

    /**
     * @var ShippingHelper $shippingHelper
     */
    protected $shippingHelper;

    /**
     * @var Onepage $onepage
     */
    protected $onepage;

    /**
     * @param Context $context
     * @param ShippingHelper $shippingHelper
     * @param Onepage $onepage
     */
    public function __construct(
        Context $context,
        ShippingHelper $shippingHelper,
        Onepage $onepage
    ) {
        parent::__construct($context);
        $this->shippingHelper = $shippingHelper;
        $this->onepage = $onepage;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Layout\Interceptor $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

        /** @var \Colissimo\Shipping\Block\Frontend\Pickup\Load $block */
        $block = $result->getLayout()->getBlock('colissimo_pickup_load');
        $block->setData(
            $this->getAddress()
        );

        return $result;
    }

    /**
     * Retrieve current address
     *
     * @return array
     */
    protected function getAddress()
    {
        $shipping = $this->onepage->getQuote()->getShippingAddress();
        if (!$shipping->getPostcode() && $this->onepage->getCustomerSession()->isLoggedIn()) {
            $default = $this->onepage->getCustomerSession()->getCustomer()->getDefaultShippingAddress();
            if ($default) {
                $shipping = $default;
            }
        }

        $countryId = $this->shippingHelper->getCountry($shipping->getCountryId());

        $address = [
            'street'     => $shipping->getStreet()[0],
            'city'       => $shipping->getCity()      ?: 'Paris',
            'postcode'   => $shipping->getPostcode()  ?: '75001',
            'country_id' => $countryId ?: 'FR',
        ];

        $data = $this->getRequest()->getParams();

        if (count($data)) {
            $address = [
                'street'     => $data['street'],
                'city'       => $data['city'],
                'postcode'   => $data['postcode'],
                'country_id' => $data['country_id'],
            ];
        }

        return $address;
    }
}