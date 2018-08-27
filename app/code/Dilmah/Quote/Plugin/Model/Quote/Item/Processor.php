<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Quote
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Quote\Plugin\Model\Quote\Item;

use \Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class Processor
 */
class Processor
{

    /**
     * Function aroundPrepare - Set qty and custom price for quote item
     * @param Item\Processor $subject
     * @param \Closure       $proceed
     * @param Item           $item
     * @param DataObject     $request
     * @param Product        $candidate
     *
     * @return void
     * @SuppressWarnings("unused")
     */
    public function aroundPrepare(
        \Magento\Quote\Model\Quote\Item\Processor $subject,
        \Closure $proceed,
        Item $item,
        DataObject $request,
        Product $candidate
    ) {
        /**
         * We specify qty after we know about parent (for stock)
         */
        if ($request->getResetCount() && !$candidate->getStickWithinParent()
            && $item->getId() == $request->getId()
        ) {
            $item->setData(CartItemInterface::KEY_QTY, 0);
        }
        $item->addQty($candidate->getCartQty());

        $customPrice = $request->getCustomPrice();

        if (empty($customPrice)) { // adding custom price for mix and match products
            if ($candidate->getData(\Dilmah\Catalog\Helper\Data::IS_COMBO_PRODUCT_ATTRIBUTE) == 1
                && !empty($request->getUnits())
            ) {
                $customPrice = $candidate->getFinalPrice() * $request->getUnits();
            }
        }

        if (!empty($customPrice)) {
            $item->setCustomPrice($customPrice);
            $item->setOriginalCustomPrice($customPrice);
        }
    }
}
