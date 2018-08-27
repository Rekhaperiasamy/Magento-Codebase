<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Directory\Model\Config\Source\Allregion;
use Magento\Shipping\Model\Config\Source\Allmethods as ShippingSourceAllmethods;
use Magento\Payment\Model\Config\Source\Allmethods as PaymentSourceAllmethods;
use Magento\Rule\Model\Condition\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

/**
 * Class Address
 */
class Address extends AbstractCondition
{
    /**
     * @var Country
     */
    protected $_directoryCountry;

    /**
     * @var Allregion
     */
    protected $_directoryAllregion;

    /**
     * @var ShippingSourceAllmethods
     */
    protected $_shippingAllmethods;

    /**
     * @var PaymentSourceAllmethods
     */
    protected $_paymentAllmethods;

    /**
     * @param Context $context
     * @param Country $directoryCountry
     * @param Allregion $directoryAllregion
     * @param ShippingSourceAllmethods $shippingAllmethods
     * @param PaymentSourceAllmethods $paymentAllmethods
     * @param array $data
     */
    public function __construct(
        Context $context,
        Country $directoryCountry,
        Allregion $directoryAllregion,
        ShippingSourceAllmethods $shippingAllmethods,
        PaymentSourceAllmethods $paymentAllmethods,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_directoryCountry = $directoryCountry;
        $this->_directoryAllregion = $directoryAllregion;
        $this->_shippingAllmethods = $shippingAllmethods;
        $this->_paymentAllmethods = $paymentAllmethods;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'base_subtotal' => __('Subtotal excl. tax'),
            'base_subtotal_with_discount' => __('Subtotal excl. tax with discount'),
            'base_subtotal_total_incl_tax' => __('Subtotal incl. tax'),
            'grand_total' => __('Subtotal incl. tax with discount'),
            'total_qty' => __('Total Items Quantity'),
            'weight' => __('Total Weight'),
            'postcode' => __('Shipping Postcode'),
            'region' => __('Shipping Region'),
            'region_id' => __('Shipping State/Province'),
            'country_id' => __('Shipping Country'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Get attribute element
     *
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal':
            case 'subtotal_incl_tax':
            case 'weight':
            case 'total_qty':
                return 'numeric';

            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }
        return 'string';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }
        return 'text';
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = $this->_directoryCountry->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_directoryAllregion->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = $this->_shippingAllmethods->toOptionArray();
                    break;

                case 'payment_method':
                    $options = $this->_paymentAllmethods->toOptionArray();
                    break;

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        $address = $model;
        if (!$address instanceof QuoteAddress) {
            if ($model->getQuote()->isVirtual()) {
                $address = $model->getQuote()->getBillingAddress();
            } else {
                $address = $model->getQuote()->getShippingAddress();
            }
        }

        if ('payment_method' == $this->getAttribute() && !$address->hasPaymentMethod()) {
            $address->setPaymentMethod($model->getQuote()->getPayment()->getMethod());
        }

        return parent::validate($address);
    }
}
