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

class OptionPayment implements \Magento\Framework\Data\OptionSourceInterface
{
    const COFIDIS_EURO = '1euro';
    const COFIDIS_TXCB = '3xcb';
    const COFIDIS_FXCB = '4xcb';
    const PAYPAL = 'paypal';

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
            self::COFIDIS_EURO => __('Cofidis 1euro Payment'),
            self::COFIDIS_TXCB => __('Cofidis 3xCB Payment'),
            self::COFIDIS_FXCB => __('Cofidis 4xCB Payment'),
            self::PAYPAL => __('PayPal Payment'),
        ];
    }
}
