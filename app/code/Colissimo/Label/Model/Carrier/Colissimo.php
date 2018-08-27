<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Model\Carrier;

use Colissimo\Shipping\Model\Carrier\Colissimo as CarrierColissimo;
use Colissimo\Shipping\Helper\Data as ShippingHelper;
use Colissimo\Label\Helper\Data as LabelHelper;
use Colissimo\Label\Model\Config\Source\Insurance;
use Magento\Framework\App\State as AppState;
use Colissimo\Label\Model\Label;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Shipping\Model\Rate\ResultFactory as RateResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory as RateMethodFactory;
use Magento\Shipping\Model\Tracking\ResultFactory as TrackingResultFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory as ResultStatusFactory;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;

/**
 * Class Colissimo
 */
class Colissimo extends CarrierColissimo
{

    /**
     * @var Label $label
     */
    protected $label;

    /**
     * @var Insurance $insurance
     */
    protected $insurance;

    /**
     * @var LabelHelper $labelHelper
     */
    protected $labelHelper;

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
     * @param Label $label
     * @param Insurance $insurance
     * @param LabelHelper $labelHelper
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
        Label $label,
        Insurance $insurance,
        LabelHelper $labelHelper,
        array $data = []
    ) {
        $this->label = $label;
        $this->insurance = $insurance;
        $this->labelHelper = $labelHelper;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $rateResultFactory,
            $rateMethodFactory,
            $shippingHelper,
            $trackFactory,
            $trackStatusFactory,
            $eventManager,
            $appState,
            $data
        );
    }

    /**
     * Return container types of carrier
     *
     * @param DataObject $params
     * @return array
     */
    public function getContainerTypes(DataObject $params = null)
    {
        $insurances = $this->insurance->toOptionArray();
        $options = [];

        if ($params) {
            $default = $this->labelHelper->getInsurance($this->_code, $params->getData('method'));
            foreach ($insurances as $insurance) {
                if ($default == $insurance['value']) {
                    $options[$insurance['value']] = $insurance['label'];
                }
            }
        }

        foreach ($insurances as $insurance) {
            if (!isset($options[$insurance['value']])) {
                $options[$insurance['value']] = $insurance['label'];
            }
        }

        return $options;
    }

    /**
     * Check if carrier has shipping label option available
     *
     * @return boolean
     */
    public function isShippingLabelsAvailable()
    {
        return true;
    }

    /**
     * Do request to shipment
     *
     * @param \Magento\Shipping\Model\Shipment\Request $request
     * @return array|\Magento\Framework\DataObject
     */
    public function requestToShipment($request)
    {
        return $this->label->doShipmentRequest($request);
    }
}
