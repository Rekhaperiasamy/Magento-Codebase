<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model\Carrier;

use Colissimo\Shipping\Helper\Data as ShippingHelper;
use Magento\Framework\App\State as AppState;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Shipping\Model\Rate\ResultFactory as RateResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory as RateMethodFactory;
use Magento\Shipping\Model\Tracking\ResultFactory as TrackingResultFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory as ResultStatusFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Colissimo
 */
class Colissimo extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string $_code
     */
    protected $_code = 'colissimo';

    /**
     * @var bool $isFixed
     */
    protected $isFixed = true;

    /**
     * @var RateResultFactory $rateResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var RateMethodFactory $rateMethodFactory
     */
    protected $rateMethodFactory;

    /**
     * @var ShippingHelper $shippingHelper
     */
    protected $shippingHelper;

    /**
     * @var TrackingResultFactory $trackFactory
     */
    protected $trackFactory;

    /**
     * @var ResultStatusFactory $trackStatusFactory
     */
    protected $trackStatusFactory;

    /**
     * @var AppState $appState
     */
    protected $appState;

    /**
     * @var ManagerInterface $eventManager
     */
    protected $eventManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param RateResultFactory $rateResultFactory
     * @param RateMethodFactory $rateMethodFactory
     * @param ShippingHelper $shippingHelper
     * @param TrackingResultFactory $trackFactory
     * @param ResultStatusFactory $trackStatusFactory
     * @param AppState $appState
     * @param ManagerInterface $eventManager
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        RateResultFactory $rateResultFactory,
        RateMethodFactory $rateMethodFactory,
        ShippingHelper $shippingHelper,
        TrackingResultFactory $trackFactory,
        ResultStatusFactory $trackStatusFactory,
        ManagerInterface $eventManager,
        AppState $appState,
        array $data = []
    ) {
        $this->rateResultFactory  = $rateResultFactory;
        $this->rateMethodFactory  = $rateMethodFactory;
        $this->shippingHelper    = $shippingHelper;
        $this->trackFactory       = $trackFactory;
        $this->trackStatusFactory = $trackStatusFactory;
        $this->appState           = $appState;
        $this->eventManager       = $eventManager;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $methods = array_keys($this->getAllowedMethods());

        if ($this->appState->getAreaCode() == FrontNameResolver::AREA_CODE) {
            $methods = array_diff($methods, ['pickup']);
        }

        return $this->addMethods($request, $methods);
    }

    /**
     * @param RateRequest $request
     * @param array $methodCodes
     * @return Result
     */
    protected function addMethods(RateRequest $request, $methodCodes)
    {
        /** @var Result $result */
        $result = $this->rateResultFactory->create();

        foreach ($methodCodes as $methodCode) {
            /* Check if method is active */
            if (!$this->getConfigData($methodCode . '/active')) {
                continue;
            }

            $countryId = $this->shippingHelper->getCountry($request->getDestCountryId(), $request->getDestPostcode());

            /* Check if country is active */
            $specific = $this->getConfigData($methodCode . '/specificcountry');
            if (!$specific) {
                continue;
            }
            $countries = explode(',', $specific);
            if (!in_array($countryId, $countries)) {
                continue;
            }

            /* Check Weight */
            $shippingWeight = $request->getPackageWeight();
            $maxWeight = $this->shippingHelper->convertWeightFromGramToStoreUnit(
                $this->shippingHelper->getConfig($methodCode . '/country/' . $countryId . '/max_weight')
            );
            if ($shippingWeight >= $maxWeight) {
                continue;
            }

            $finalPrice = 0;

            if ($request->getFreeShipping() !== true) {
                $prices = $this->getPriceData($methodCode, $countryId);
                foreach ($prices as $price) {
                    $minRange = $shippingWeight >= $price['weight_from'];
                    $maxRange = !$price['weight_to'] || $shippingWeight < $price['weight_to'];

                    if ($minRange && $maxRange) {
                        $finalPrice = $price['price'];
                        break;
                    }
                }
            }

            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($methodCode);
            $method->setMethodTitle($this->getConfigData($methodCode . '/name'));

            $method->setPrice($finalPrice);
            $method->setCost($finalPrice);

            $this->eventManager->dispatch(
                'colissimo_append_method', ['method' => $method, 'request' => $request]
            );

            if ($method->getHideMethod()) {
                continue;
            }

            $result->append($method);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        $methods = [
            'homecl'        => $this->getConfigData('homecl/name'),
            'homesi'        => $this->getConfigData('homesi/name'),
            'international' => $this->getConfigData('international/name'),
            'domtomcl'      => $this->getConfigData('domtomcl/name'),
            'domtomsi'      => $this->getConfigData('domtomsi/name'),
            'pickup'        => $this->getConfigData('pickup/name'),
        ];

        return $methods;
    }

    /**
     * Check if carrier has shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Get tracking information
     *
     * @param string $tracking
     * @return string|false
     * @api
     */
    public function getTrackingInfo($tracking)
    {
        $result = $this->getTracking($tracking);

        if ($result instanceof \Magento\Shipping\Model\Tracking\Result) {
            $trackings = $result->getAllTrackings();
            if ($trackings) {
                return $trackings[0];
            }
        } elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * Get tracking
     *
     * @param string|string[] $trackings
     * @return Result
     */
    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        $result = $this->trackFactory->create();

        foreach ($trackings as $tracking) {
            /** @var \Magento\Shipping\Model\Tracking\Result\Status $status */
            $status = $this->trackStatusFactory->create();
            $status->setCarrier($this->_code);
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setPopup(1);
            $status->setUrl("https://www.laposte.fr/particulier/outils/suivre-vos-envois?code={$tracking}");

            $result->append($status);
        }

        return $result;
    }

    /**
     * @param DataObject $request
     * @return $this
     */
    public function checkAvailableShipCountries(DataObject $request)
    {
        return $this;
    }

    /**
     * Retrieve price data
     *
     * @param string $method
     * @param string $countryId
     * @return array
     */
    public function getPriceData($method, $countryId)
    {
        $price = $this->getConfigData($method . '/price');
        $final = [];

        try {
            $prices = unserialize($price);
        } catch (\Exception $e) {
            $prices = [];
        }

        // Since Magento 2.2
        if (!count($prices) && json_decode($price)) {
            $prices = json_decode($price, true);
        }

        foreach ($prices as $data) {
            if ($countryId == $data['country']) {
                $final[$data['weight_from'] ? $data['weight_from'] * 1000 : 0] = $data;
            }
        }
        ksort($final);

        return $final;
    }
}
