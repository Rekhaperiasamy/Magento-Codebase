<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Plugin\Block\Product;

/**
 * Class AbstractProduct
 */
class AbstractProduct
{

    /**
     * @var \Magento\CatalogInventory\Block\Stockqty\DefaultStockqty
     */
    protected $stockQty;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * AbstractProduct constructor.
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\CatalogInventory\Block\Stockqty\DefaultStockqty $stockQty
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\CatalogInventory\Block\Stockqty\DefaultStockqty $stockQty
    ) {
        $this->eventManager = $context->getEventManager();
        $this->stockQty = $stockQty;
    }

    /**
     * Get if it is necessary to show product stock status
     *
     * @param \Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param \Closure $proceed
     * @return bool
     * @SuppressWarnings("unused")
     */
    public function aroundDisplayProductStockStatus(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        \Closure $proceed
    ) {
        $statusInfo = new \Magento\Framework\DataObject(['display_status' => true]);
        $this->eventManager->dispatch('catalog_block_product_status_display', ['status' => $statusInfo]);
        return (bool)$statusInfo->getDisplayStatus() && !$this->stockQty->isMsgVisible();
    }
}
