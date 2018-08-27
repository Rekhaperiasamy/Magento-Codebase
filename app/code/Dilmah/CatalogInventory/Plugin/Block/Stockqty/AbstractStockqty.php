<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_CatalogInventory
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\CatalogInventory\Plugin\Block\Stockqty;

/**
 * Class AbstractStockqty
 */
class AbstractStockqty
{
    /**
     * wholesale customer group id
     */
    const WHOLESALE_CUSTOMER_GROUP_ID = 2;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * AbstractStockqty constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * Retrieve visibility of stock qty message
     *
     * @param \Magento\CatalogInventory\Block\Stockqty\AbstractStockqty $subject
     * @param \Closure $proceed
     * @return bool
     * @SuppressWarnings("unused")
     */
    public function aroundIsMsgVisible(
        \Magento\CatalogInventory\Block\Stockqty\AbstractStockqty $subject,
        \Closure $proceed
    ) {
        $customer = $this->customerSession->getCustomer();
        if ($customer->getId()
            && $customer->getGroupId() == self::WHOLESALE_CUSTOMER_GROUP_ID
        ) { // if customer is logged in and a wholesale customer
            return $subject->getStockQty() > 0;
        }
        return $subject->getStockQty() > 0 && $subject->getStockQtyLeft() <= $subject->getThresholdQty();
    }
}
