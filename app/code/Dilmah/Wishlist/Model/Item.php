<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Wishlist
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Wishlist\Model;

class Item extends \Magento\Wishlist\Model\Item
{

    /**
     * Add or Move item product to shopping cart
     *
     * Return true if product was successful added or exception with code
     * Return false for disabled or unvisible products
     *
     * @param \Magento\Checkout\Model\Cart $cart
     * @param bool|false $delete
     * @return bool
     * @throws ProductException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addToCart(\Magento\Checkout\Model\Cart $cart, $delete = false)
    {
        $product = $this->getProduct();

        $storeId = $this->getStoreId();

        if ($product->getStatus() != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
            return false;
        }

        if (!$product->isVisibleInSiteVisibility()) {
            if ($product->getStoreId() == $storeId) {
                return false;
            }
            $urlData = $this->_catalogUrl->getRewriteByProductStore([$product->getId() => $storeId]);
            if (!isset($urlData[$product->getId()])) {
                return false;
            }
            $product->setUrlDataObject(new \Magento\Framework\DataObject($urlData));
            $visibility = $product->getUrlDataObject()->getVisibility();
            if (!in_array($visibility, $product->getVisibleInSiteVisibilities())) {
                return false;
            }
        }

        if (!$product->isSalable()) {
            throw new ProductException(__('Product is not salable.'));
        }

        $buyRequest = $this->getBuyRequest();

        //Add pack products to the cart
        if ($product->getTypeId() == 'bundle' &&
            $product->getData(\Dilmah\Catalog\Helper\Data::IS_PACK_PRODUCT_ATTRIBUTE) == 1
        ) {
            $data = $buyRequest->getData();
            $selection = $this->getFirstSelectionItemForBundleProduct($product);
            $optionArr[$selection->getOptionId()] = $selection->getSelectionId();
            $data['bundle_option'] = $optionArr;
            $buyRequest->setData($data);
        }

        $cart->addProduct($product, $buyRequest);
        if (!$product->isVisibleInSiteVisibility()) {
            $cart->getQuote()->getItemByProduct($product)->setStoreId($storeId);
        }

        if ($delete) {
            $this->delete();
        }

        return true;
    }

    /**
     * Get first Selection Items for bundle products
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getFirstSelectionItemForBundleProduct($product)
    {
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );
        $selection = $selectionCollection->getFirstItem();

        return $selection;
    }
}
