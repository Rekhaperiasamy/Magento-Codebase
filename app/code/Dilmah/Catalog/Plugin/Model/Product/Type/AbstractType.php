<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Plugin\Model\Product\Type;

class AbstractType
{
    /**
     * Get pack bundle products as normal product by returning as without required options
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     * @SuppressWarnings("unused")
     */
    public function aroundHasRequiredOptions(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        $product
    ) {
        if ($product->getTypeId() == 'bundle' &&
            $product->getData(\Dilmah\Catalog\Helper\Data::IS_PACK_PRODUCT_ATTRIBUTE) == 1
        ) {
            return false;
        }
        if ($product->getRequiredOptions()) {
            return true;
        }
        return false;
    }
}
