<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Checkout\CustomerData;

/**
 * Default item
 */
class DefaultItem extends \Magento\Checkout\CustomerData\DefaultItem
{

    /**
     * {@inheritdoc}
     */
    protected function doGetItemData()
    {
        $product = $this->item->getProduct();
        $imageHelper = $this->imageHelper->init($this->getProductForThumbnail(), 'mini_cart_product_thumbnail');
        $isPack = $product->getData(\Dilmah\Catalog\Helper\Data::IS_PACK_PRODUCT_ATTRIBUTE) == 1;
        $isCombo = $product->getData(\Dilmah\Catalog\Helper\Data::IS_COMBO_PRODUCT_ATTRIBUTE) == 1;
        $packSize = empty($size = $product->getData(\Dilmah\Catalog\Helper\Data::PACK_SIZE_ATTRIBUTE)) ? 1 : $size;
        $isMixMatchPromo = $product->getData(\Dilmah\Catalog\Helper\Data::IS_MIX_AND_MATCH_PROMOTION) == 1;
        return [
            'options' => $this->getOptionList(),
            'qty' => $this->item->getQty() * 1,
            'item_id' => $this->item->getId(),
            'configure_url' => $this->getConfigureUrl(),
            'is_visible_in_site_visibility' => $this->item->getProduct()->isVisibleInSiteVisibility(),
            'product_id' => $this->item->getProduct()->getId(),
            'product_name' => $this->item->getProduct()->getName(),
            'product_url' => $this->getProductUrl(),
            'product_has_url' => $this->hasProductUrl(),
            'product_price' => $this->checkoutHelper->formatPrice($this->item->getCalculationPrice()),
            'product_image' => [
                'src' => $imageHelper->getUrl(),
                'alt' => $imageHelper->getLabel(),
                'width' => $imageHelper->getWidth(),
                'height' => $imageHelper->getHeight(),
            ],
            'canApplyMsrp' => $this->msrpHelper->isShowBeforeOrderConfirm($this->item->getProduct())
                && $this->msrpHelper->isMinimalPriceLessMsrp($this->item->getProduct()),
            'is_pack' => $isPack,
            'is_combo' => $isCombo,
            'pack_size' => ($this->item->getQty() * 1) * $packSize,
            'is_mix_match_promo' => $isMixMatchPromo
        ];
    }
}
