<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Model\Config\Source;

use Blackbird\Monetico\Model\Method\Onetime;
use Blackbird\Monetico\Model\Method\Multitime;
use Blackbird\Monetico\Model\Method\CofidisEuro;
use Blackbird\Monetico\Model\Method\CofidisTxcb;
use Blackbird\Monetico\Model\Method\CofidisFxcb;
use Blackbird\Monetico\Model\Method\PayPal;

class PaymentMethod implements \Magento\Framework\Data\OptionSourceInterface
{
    const ONETIME = Onetime::METHOD_CODE;
    const MULTITIME = Multitime::METHOD_CODE;
    const COFIDIS_EURO = CofidisEuro::METHOD_CODE;
    const COFIDIS_TXCB = CofidisTxcb::METHOD_CODE;
    const COFIDIS_FXCB = CofidisFxcb::METHOD_CODE;
    const PAYPAL = PayPal::METHOD_CODE;

    /**
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $options = [];
            $paymentMethods = $this->getOptions();

            foreach ($paymentMethods as $value => $label) {
                $options[] = [
                    'value' => $value,
                    'label' => $label,
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }

    /**
     * Get the list of the available payment methods
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            self::ONETIME => __('Onetime Payment'),
            self::MULTITIME => __('Multi-time Payment'),
            self::COFIDIS_EURO => __('Cofidis 1euro Payment'),
            self::COFIDIS_TXCB => __('Cofidis 3xCB Payment'),
            self::COFIDIS_FXCB => __('Cofidis 4xCB Payment'),
            self::PAYPAL => __('PayPal Payment'),
        ];
    }

    /**
     * Retrieve the service name for the payment method
     *
     * @param string $method
     * @return string
     */
    public function getServiceName($method)
    {
        $serviceName = 'Monetico';

        switch ($method) {
            case self::ONETIME:
            case self::MULTITIME:
                $serviceName = 'CM-CIC';
                break;
            case self::COFIDIS_EURO:
            case self::COFIDIS_TXCB:
            case self::COFIDIS_FXCB:
                $serviceName = 'Cofidis';
                break;
            case self::PAYPAL:
                $serviceName = 'PayPal';
                break;
        }

        return $serviceName;
    }

    /**
     * Get the available payment methods codes
     *
     * @return array
     */
    public function getAvailablePaymentMethods()
    {
        return array_keys($this->getOptions());
    }

    /**
     * Retrieve the translation for a payment method code
     *
     * @param $methodCode
     * @return bool|mixed
     */
    public function getPaymentMethodTranslation($methodCode)
    {
        $options = $this->getOptions();

        return isset($options[$methodCode]) ? $options[$methodCode] : false;
    }
}
