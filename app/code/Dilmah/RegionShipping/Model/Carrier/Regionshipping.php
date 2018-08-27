<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah_RegionShipping
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

/**
 * Region shipping model.
 */
namespace Dilmah\RegionShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Regionshipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'regionshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * RegionShipping Rates Collector.
     *
     * @param RateRequest $request
     *
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $this->_updateFreeMethodQuote($request);

        if ($request->getRegionShipping() || $request->getBaseSubtotalInclTax() >= $this->getConfigData(
            'region_shipping_subtotal'
        )
        ) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('regionshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('regionshipping');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        }

        return $result;
    }

    /**
     * Allows region shipping when all product items have free shipping (promotions etc.).
     *
     * @param RateRequest $request
     * @return null
     */
    protected function _updateFreeMethodQuote($request)
    {
        $regionShipping = false;
        $items = $request->getAllItems();
        $c = count($items);
        for ($i = 0; $i < $c; ++$i) {
            if ($items[$i]->getProduct() instanceof \Magento\Catalog\Model\Product) {
                if ($items[$i]->getRegionShipping()) {
                    $regionShipping = true;
                } else {
                    return;
                }
            }
        }
        if ($regionShipping) {
            $request->setRegionShipping(true);
        }
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['regionshipping' => $this->getConfigData('name')];
    }

    /**
     * @param \Magento\Framework\DataObject $request
     *
     * @return $this|bool|false|\Magento\Framework\Model\AbstractModel
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function checkAvailableShipCountries(\Magento\Framework\DataObject $request)
    {
        $speCountriesAllow = $this->getConfigData('sallowspecific');
        /*
         * for specific countries, the flag will be 1
         */
        if ($speCountriesAllow && $speCountriesAllow == 1) {
            $showMethod = $this->getConfigData('showmethod');
            $availableCountries = [];
            $availableRegions = [];
            if ($this->getConfigData('country_id')) {
                $availableCountries = explode(',', $this->getConfigData('country_id'));
            }
            if ($this->getConfigData('region_id')) {
                $availableRegions = explode(',', $this->getConfigData('region_id'));
            }
            if ($availableCountries && in_array($request->getDestCountryId(), $availableCountries)
                && $availableRegions && in_array($request->getDestRegionId(), $availableRegions)
            ) {
                return $this;
            } elseif ($showMethod && (
                    !$availableCountries || $availableCountries && !in_array(
                        $request->getDestCountryId(),
                        $availableCountries
                    )
                ) && (
                    !$availableRegions || $availableRegions && !in_array(
                        $request->getDestRegionId(),
                        $availableRegions
                    )
                )
            ) {
                /** @var Error $error */
                $error = $this->_rateErrorFactory->create();
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $errorMsg = $this->getConfigData('specificerrmsg');
                $error->setErrorMessage(
                    $errorMsg ? $errorMsg : __(
                        'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                    )
                );

                return $error;
            } else {
                /*
                 * The admin set not to show the shipping module if the delivery country is not within specific
                 * countries
                 */
                return false;
            }
        }

        return $this;
    }
}
