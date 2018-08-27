<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block;

class Frauddetection extends \Magento\Framework\View\Element\Template
{
    const TRACKING_CODE_APPLICATION_ID = "10376";

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    protected $oPSOrderHelper;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $onepageCheckout;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Netresearch\OPS\Helper\Order $oPSOrderHelper
     * @param \Magento\Checkout\Model\Type\Onepage $onepageCheckout
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Magento\Checkout\Model\Type\Onepage $onepageCheckout,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->customerSession = $customerSession;
        $this->oPSOrderHelper = $oPSOrderHelper;
        $this->onepageCheckout = $onepageCheckout;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netresearch_OPS::ops/frauddetection.phtml');
    }

    /**
     * renders the additional fraud detection js
     * @return string
     */
    protected function _toHtml()
    {
        $html = null;
        $storeId = $this->_storeManager->getStore()->getId();
        $fingerptintConsentSessionKey = $this->customerSession
            ->getData(\Netresearch\OPS\Model\Payment\PaymentAbstract::FINGERPRINT_CONSENT_SESSION_KEY);
        if ($this->oPSConfigFactory->create()->getDeviceFingerPrinting($storeId) && $fingerptintConsentSessionKey) {
            $html = parent::_toHtml();
        }
        return $html;
    }

    /**
     * get the tracking code application id from config
     *
     * @return string
     */
    public function getTrackingCodeAid()
    {
        return self::TRACKING_CODE_APPLICATION_ID;
    }

    /**
     * build md5 hash from customer session ID
     *
     * @return string
     */
    public function getTrackingSid()
    {
        $quote = $this->onepageCheckout->getQuote();
        $inputString = $this->oPSConfigFactory->create()->getPSPID($quote->getStoreId())
            . $this->oPSOrderHelper->getOpsOrderId($quote);

        return md5($inputString);
    }
}
