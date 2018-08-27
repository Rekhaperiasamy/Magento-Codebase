<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Wishlist
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Wishlist\CustomerData;

/**
 * Wishlist section
 */
class Wishlist extends \Magento\Wishlist\CustomerData\Wishlist
{

    /**
     * Retrieve wishlist item data
     *
     * @param \Magento\Wishlist\Model\Item $wishlistItem
     *
     * @return array
     */
    protected function getItemData(\Magento\Wishlist\Model\Item $wishlistItem)
    {
        $product = $wishlistItem->getProduct();
        return [
            'image' => $this->getImageData($product),
            'product_url' => $this->wishlistHelper->getProductUrl($wishlistItem),
            'product_name' => $product->getName(),
            'product_price' => $this->block->getProductPrice($product),
            'product_is_saleable_and_visible' => $product->isSaleable() && $product->isVisibleInSiteVisibility(),
            'product_has_required_options' => $product->getTypeInstance()->hasRequiredOptions($product),
            'add_to_cart_params' => $this->wishlistHelper->getAddToCartParams($wishlistItem, true),
            'delete_item_params' => $this->wishlistHelper->getRemoveParams($wishlistItem, true),
            'is_sale' => $product->getData(\Dilmah\Catalog\Helper\Data::LABEL_MANAGER_SALE_ATTRIBUTE) == 1,
            'is_new' => $product->getData(\Dilmah\Catalog\Helper\Data::LABEL_MANAGER_NEW_ATTRIBUTE) == 1,
            'is_promo' => $product->getData(\Dilmah\Catalog\Helper\Data::LABEL_MANAGER_PROMO_ATTRIBUTE) == 1,
        ];
    }
}
