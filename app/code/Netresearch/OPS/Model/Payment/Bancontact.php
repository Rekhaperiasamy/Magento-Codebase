<?php

/**
 * Netresearch OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright   Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license     Open Software License (OSL 3.0)
 * @link        http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Model\Payment;

/**
 * @category    Ingenico
 * @package     Netresearch_OPS
 * @author      Sebastian Ertner <sebastian.ertner@netresearch.de>
 */
class Bancontact extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    const CODE = 'ops_BCMC';

    /**
     * @var null|\Netresearch\OPS\Helper\MobileDetect
     */
    protected $oPSMobileDetectHelper = null;
    protected $pm                    = 'CreditCard';
    protected $brand                 = 'BCMC';

    /**
     * Check if we can capture directly from the backend
     */
    protected $_canBackendDirectCapture = true;

    /**
     * Payment Code
     */
    protected $_code = self::CODE;

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Bancontact';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Magento\Framework\Stdlib\StringUtils $stringUtils,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Netresearch\OPS\Model\Backend\Operation\ParameterFactory $oPSBackendOperationParameterFactory,
        \Netresearch\OPS\Model\Config $oPSConfig,
        \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Order\Capture $oPSOrderCaptureHelper,
        \Netresearch\OPS\Model\Api\DirectLink $oPSApiDirectlink,
        \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper,
        \Netresearch\OPS\Model\Status\UpdateFactory $oPSStatusUpdateFactory,
        \Netresearch\OPS\Helper\Order\Refund $oPSOrderRefundHelper,
        \Netresearch\OPS\Model\Response\Handler $oPSResponseHandler,
        \Netresearch\OPS\Model\Validator\Parameter\FactoryFactory $oPSValidatorParameterFactoryFactory,
        \Netresearch\OPS\Helper\Validation\Result $oPSValidationResultHelper,
        \Netresearch\OPS\Helper\MobileDetect $oPSMobileDetectHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        \Magento\Payment\Model\Method\AbstractMethod::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->storeManager                        = $storeManager;
        $this->checkoutSession                     = $checkoutSession;
        $this->salesOrderFactory                   = $salesOrderFactory;
        $this->stringUtils                         = $stringUtils;
        $this->request                             = $request;
        $this->customerSession                     = $customerSession;
        $this->messageManager                      = $messageManager;
        $this->oPSBackendOperationParameterFactory = $oPSBackendOperationParameterFactory;
        $this->oPSConfig                           = $oPSConfig;
        $this->oPSPaymentRequestHelper             = $oPSPaymentRequestHelper;
        $this->oPSOrderHelper                      = $oPSOrderHelper;
        $this->oPSHelper                           = $oPSHelper;
        $this->oPSPaymentHelper                    = $oPSPaymentHelper;
        $this->oPSOrderCaptureHelper               = $oPSOrderCaptureHelper;
        $this->oPSApiDirectlink                    = $oPSApiDirectlink;
        $this->oPSDirectlinkHelper                 = $oPSDirectlinkHelper;
        $this->oPSStatusUpdateFactory              = $oPSStatusUpdateFactory;
        $this->oPSOrderRefundHelper                = $oPSOrderRefundHelper;
        $this->oPSResponseHandler                  = $oPSResponseHandler;
        $this->oPSValidatorParameterFactoryFactory = $oPSValidatorParameterFactoryFactory;
        $this->oPSValidationResultHelper           = $oPSValidationResultHelper;
        $this->oPSMobileDetectHelper               = $oPSMobileDetectHelper;
    }

    /**
     * add needed params to dependend formfields
     *
     * @param Mage_Sales_Model_Order $order
     * @param null                   $requestParams
     *
     * @return \string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $formFields           = parent::getMethodDependendFormFields($order, $requestParams);
        $formFields['DEVICE'] = $this->getInfoInstance()->getAdditionalInformation('DEVICE');

        return $formFields;
    }

    /**
     * @param \Magento\Framework\DataObject $data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $payment = $this->getInfoInstance();
        $payment->setAdditionalInformation('DEVICE', $this->getMobileDetectHelper()->getDeviceType());

        return $this;
    }

    /**
     * Get Mobile Detect Helper
     *
     * @return \Netresearch\OPS\Helper\MobileDetect
     */
    public function getMobileDetectHelper()
    {
        return $this->oPSMobileDetectHelper;
    }

    /**
     * @param Netresearch_OPS_Helper_MobileDetect $mobileHelper
     *
     * @returns $this
     */
    public function setMobileDetectHelper($mobileHelper)
    {
        $this->oPSMobileDetectHelper = $mobileHelper;

        return $this;
    }
}
