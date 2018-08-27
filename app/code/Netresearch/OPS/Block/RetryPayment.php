<?php
/**
 * Netresearch_OPS
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
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Block;

/**
 * RetryPayment.php
 *
 * @category payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */

class RetryPayment extends \Netresearch\OPS\Block\Placeform
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    protected $oPSOrderHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        array $data = []
    ) {
        parent::__construct($context, $oPSConfigFactory, $checkoutSession, $salesOrderFactory, $data);
        $this->oPSOrderHelper = $oPSOrderHelper;
    }

    protected function _getApi()
    {
        return $this->_getOrder()->getPayment()->getMethodInstance();
    }

    /**
     * @return \Magento\Sales\Model\Order|null
     */
    protected function _getOrder()
    {
        if (null === $this->order) {
            $opsOrderId = $this->getRequest()->getParam('orderID');
            $this->order = $this->oPSOrderHelper->getOrder($opsOrderId);
        }
        return $this->order;
    }
}
