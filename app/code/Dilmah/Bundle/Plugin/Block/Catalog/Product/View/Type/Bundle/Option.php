<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Bundle
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Bundle\Plugin\Block\Catalog\Product\View\Type\Bundle;

/**
 * Class Option
 */
class Option extends \Magento\CatalogInventory\Block\Stockqty\AbstractStockqty
{

    /**
     * wholesale customer group id
     */
    const WHOLESALE_CUSTOMER_GROUP_ID = 2;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Option constructor.
     *
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->imageHelper = $imageHelper;
        $this->stockRegistry = $stockRegistry;
        $this->customerSession = $customerSession;
    }

    /**
     * around plugin for checkboxes
     *
     * @param \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $selection
     * @return string
     * @SuppressWarnings("unused")
     */
    public function aroundGetSelectionQtyTitlePrice(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject,
        \Closure $proceed,
        $selection
    ) {
        $imageId = 'bundle_product_option_thumbnail';
        $productName = $subject->escapeHtml($selection->getName());
        $subject->setFormatProduct($selection);
        $priceTitle = '<div class="product-option">';
        $priceTitle .= '<div class="product-image">';
        $imageUrl = $this->imageHelper->init($selection, $imageId)->getUrl();
        $priceTitle .= '<img src="' . $imageUrl . '" alt="' . $productName . '"/></div>';
        $priceTitle .= '<div class="product-name">' . $productName . '</div>';
        $priceTitle .= '<div class="product-qty">';
        $priceTitle .= '<span>' . $selection->getSelectionQty() * 1 . '</span>';

        $customer = $this->customerSession->getCustomer();
        if ($customer->getId()
            && $customer->getGroupId() == self::WHOLESALE_CUSTOMER_GROUP_ID
        ) { // if customer is logged in and a wholesale customer
            $currentStockItem = $this->stockRegistry->getStockItem(
                $selection->getId(),
                $selection->getStore()->getWebsiteId()
            );
            $qtyLeft = $currentStockItem->getStockQty() - $currentStockItem->getMinQty();

            $priceTitle .= '<div class="stock" title="' . __('Only %1 left', ($qtyLeft)) . '"><span>' .
                __('Only %1 left', "<strong>{$qtyLeft}</strong>") .
                '</span></div></div>';
        } else {
            $priceTitle .= '<div class="stock available" title="Availability"><span>' .
                __('In stock') .
                '</span></div></div>';
        }

        $priceTitle .= '</div>';

        return $priceTitle;
    }

    /**
     * around plugin for radio buttons
     *
     * @param \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $selection
     * @return string
     * @SuppressWarnings("unused")
     */
    public function aroundGetSelectionTitlePrice(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject,
        \Closure $proceed,
        $selection
    ) {
        $imageId = 'bundle_product_option_thumbnail';
        $productName = $subject->escapeHtml($selection->getName());
        $priceTitle = '<div class="product-option">';
        $priceTitle .= '<div class="product-image">';
        $imageUrl = $this->imageHelper->init($selection, $imageId)->getUrl();
        $priceTitle .= '<img src="' . $imageUrl . '" alt="' . $productName . '"/></div>';
        $priceTitle .= '<div class="product-name">' . $productName . '</div>';
        $priceTitle .= '<div class="product-qty">';

        if ($subject->getProduct()->getData(\Dilmah\Catalog\Helper\Data::IS_COMBO_PRODUCT_ATTRIBUTE) != 1) {
            $priceTitle .= '<span>' . $selection->getSelectionQty() * 1 . '</span>';
        }

        $customer = $this->customerSession->getCustomer();
        if ($customer->getId()
            && $customer->getGroupId() == self::WHOLESALE_CUSTOMER_GROUP_ID
        ) { // if customer is logged in and a wholesale customer
            $currentStockItem = $this->stockRegistry->getStockItem(
                $selection->getId(),
                $selection->getStore()->getWebsiteId()
            );
            $qtyLeft = $currentStockItem->getStockQty() - $currentStockItem->getMinQty();

            $priceTitle .= '<div class="stock" title="' . __('Only %1 left', ($qtyLeft)) . '"><span>' .
                __('Only %1 left', "<strong>{$qtyLeft}</strong>") .
                '</span></div></div>';
        } else {
            $priceTitle .= '<div class="stock available" title="Availability"><span>' .
                __('In stock') .
                '</span></div></div>';
        }

        $priceTitle .= '</div>';


        return $priceTitle;
    }
}
