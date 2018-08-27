<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Adminhtml\Kwixo\Shipping;

class Edit extends \Magento\Backend\Block\Template
{
    private $kwixoShippingModel = null;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @var \Netresearch\OPS\Model\Source\Kwixo\ShipMethodTypeFactory
     */
    protected $oPSSourceKwixoShipMethodTypeFactory;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Shipping\SettingFactory
     */
    protected $oPSKwixoShippingSettingFactory;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Netresearch\OPS\Model\Source\Kwixo\ShipMethodTypeFactory $oPSSourceKwixoShipMethodTypeFactory,
        \Netresearch\OPS\Model\Kwixo\Shipping\SettingFactory $oPSKwixoShippingSettingFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shippingConfig = $shippingConfig;
        $this->oPSSourceKwixoShipMethodTypeFactory = $oPSSourceKwixoShipMethodTypeFactory;
        $this->oPSKwixoShippingSettingFactory = $oPSKwixoShippingSettingFactory;
    }

    /**
     * gets the form action url
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('adminhtml/kwixoshipping/save');
    }

    /**
     * gets the shipping types
     *
     * @return array
     */
    public function getShippingMethods()
    {
        $methods = $this->shippingConfig->getAllCarriers();
        $options = [];

        foreach ($methods as $carrierCode => $carrier) {
            $title = $this->_scopeConfig
                ->getValue("carriers/$carrierCode/title", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$title) {
                $title = $carrierCode;
            }
            $values = $this->getValues($carrierCode);
            $options[] = ['code' => $carrierCode, 'label' => $title, 'values' => $values];
        }

        return $options;
    }

    /**
     * returns the corresponding shipping method types
     *
     * @return array - the kwxixo Shipping method types
     */
    public function getKwixoShippingTypes()
    {
        return $this->oPSSourceKwixoShipMethodTypeFactory->create()->toOptionArray();
    }

    public function getKwixoShippingSettingModel()
    {
        if (null === $this->kwixoShippingModel) {
            $this->kwixoShippingModel = $this->oPSKwixoShippingSettingFactory->create();
        }
        return $this->kwixoShippingModel;
    }

    private function getValues($carrierCode)
    {
        $values = [
            'kwixo_shipping_type' => '',
            'kwixo_shipping_speed' => '',
            'kwixo_shipping_details' => ''
        ];
        if (null !== $this->getData('postData') && array_key_exists($carrierCode, $this->getData('postData'))) {
            $errorData = $this->getData('postData');
            $values =  $errorData[$carrierCode];
        } else {
            $values = $this->getKwixoShippingSettingModel()->load($carrierCode, 'shipping_code')->getData();
        }
        return $values;
    }
}
