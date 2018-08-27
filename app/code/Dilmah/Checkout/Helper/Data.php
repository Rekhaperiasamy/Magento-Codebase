<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Checkout\Helper;

use \Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Dilmah\Checkout\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var null|bool
     */
    protected $hasVirtualItems;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->hasVirtualItems = null;
        parent::__construct($context);
    }

    /**
     * get virtual product message
     *
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Framework\Phrase|null
     */
    public function getVirtualProductMessage($order)
    {
        if ($this->isVirtualProductAvailable($order)) {
            return __('<p style="color: #2d9699;">Please give us 24 hours to enable your download link(s)</p>');
        }
        return null;
    }

    /**
     * check if virtual products are available in cart
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool|null
     */
    public function isVirtualProductAvailable(\Magento\Sales\Model\Order $order)
    {
        if ($this->hasVirtualItems === null) {
            $items = $order->getAllVisibleItems();
            foreach ($items as $item) {
                if ($item->getIsVirtual()) {
                    $this->hasVirtualItems = true;
                    return true;
                }
            }
            $this->hasVirtualItems = false;
            return false;
        }
        return $this->hasVirtualItems;
    }

    /**
     * Retrieve checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }
}
