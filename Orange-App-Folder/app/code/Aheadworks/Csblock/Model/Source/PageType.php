<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PageType
 * @package Aheadworks\Csblock\Model\Source
 */
class PageType implements OptionSourceInterface
{
    const HOME_PAGE = 1;
    const PRODUCT_PAGE = 2;
    const CATEGORY_PAGE = 3;
    const SHOPPINGCART_PAGE = 4;
    const CHECKOUT_PAGE = 5;
	const INTERMEDIATE_PAGE = 6;

    const HOME_PAGE_LABEL = "Home Page";
    const PRODUCT_PAGE_LABEL = "Product Pages";
    const CATEGORY_PAGE_LABEL = "Catalog Pages";
    const SHOPPINGCART_PAGE_LABEL = "Shopping Cart";
    const CHECKOUT_PAGE_LABEL = "Checkout";
	const INTERMEDIATE_PAGE_LABEL = "Intermediate Page";

    const DEFAULT_VALUE = 2;

    public function getOptionArray()
    {
        return [
            self::HOME_PAGE => __(self::HOME_PAGE_LABEL),
            self::PRODUCT_PAGE => __(self::PRODUCT_PAGE_LABEL),
            self::CATEGORY_PAGE => __(self::CATEGORY_PAGE_LABEL),
            self::SHOPPINGCART_PAGE => __(self::SHOPPINGCART_PAGE_LABEL),
            self::CHECKOUT_PAGE => __(self::CHECKOUT_PAGE_LABEL),
			self::INTERMEDIATE_PAGE => __(self::INTERMEDIATE_PAGE_LABEL)
        ];
    }

    public function toOptionArray()
    {
        return [
            ['value' => self::HOME_PAGE,  'label' => __(self::HOME_PAGE_LABEL)],
            ['value' => self::PRODUCT_PAGE,  'label' => __(self::PRODUCT_PAGE_LABEL)],
            ['value' => self::CATEGORY_PAGE,  'label' => __(self::CATEGORY_PAGE_LABEL)],
            ['value' => self::SHOPPINGCART_PAGE,  'label' => __(self::SHOPPINGCART_PAGE_LABEL)],
            ['value' => self::CHECKOUT_PAGE,  'label' => __(self::CHECKOUT_PAGE_LABEL)],
			['value' => self::INTERMEDIATE_PAGE,  'label' => __(self::INTERMEDIATE_PAGE_LABEL)],
        ];
    }
}