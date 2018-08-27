<?php

namespace Netresearch\OPS\Controller\Adminhtml;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG
 *              (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 */
abstract class Kwixoshipping extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\SessionFactory
     */
    protected $backendSessionFactory;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @var \Netresearch\OPS\Model\Validator\Kwixo\Shipping\SettingFactory
     */
    protected $oPSValidatorKwixoShippingSettingFactory;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Shipping\SettingFactory
     */
    protected $oPSKwixoShippingSettingFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\SessionFactory $backendSessionFactory,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Netresearch\OPS\Model\Validator\Kwixo\Shipping\SettingFactory $oPSValidatorKwixoShippingSettingFactory,
        \Netresearch\OPS\Model\Kwixo\Shipping\SettingFactory $oPSKwixoShippingSettingFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->backendSessionFactory = $backendSessionFactory;
        $this->shippingConfig = $shippingConfig;
        $this->oPSValidatorKwixoShippingSettingFactory = $oPSValidatorKwixoShippingSettingFactory;
        $this->oPSKwixoShippingSettingFactory = $oPSKwixoShippingSettingFactory;
        $this->backendAuthSession = $backendAuthSession;
        $this->pageFactory = $pageFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales:shipment');
    }
}
