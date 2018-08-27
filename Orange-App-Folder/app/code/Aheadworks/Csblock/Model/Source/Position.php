<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Position
 * @package Aheadworks\Csblock\Model\Source
 */
class Position implements OptionSourceInterface
{

    /**
     * constants are defined for custom sorting. ASC sorting:
     * MENU TOP/BOTTOM
     * CONTENT TOP/BOTTOM
     * SIDEBAR TOP/BOTTOM
     */

    //const SIDEBAR_TOP = 5;
    //const SIDEBAR_BOTTOM = 6;
	const BEFORE_TAB = 5;
    const PRODUCT_DETAIL = 6;
    const CONTENT_TOP = 3;
    const PAGE_BOTTOM = 4;
   // const MENU_TOP = 1;
    //const MENU_BOTTOM = 2;
	const PAGE_ACCORDION = 7;

    //const SIDEBAR_TOP_LABEL = "Sidebar top";
    //const SIDEBAR_BOTTOM_LABEL = "Sidebar bottom";
	const BEFORE_TAB_LABEL = "Before Tab";
    const PRODUCT_DETAIL_LABEL = "Product Detail";
    const CONTENT_TOP_LABEL = "Content top";
    const PAGE_BOTTOM_LABEL = "Page bottom";
    //const MENU_TOP_LABEL = "Menu Top";
   // const MENU_BOTTOM_LABEL = "Menu Bottom";
	const PAGE_ACCORDION_LABEL = "page accordion";

    const DEFAULT_VALUE = 3;

    public function getOptionArray()
    {
        return [
            //self::SIDEBAR_TOP => __(self::SIDEBAR_TOP_LABEL),
           // self::SIDEBAR_BOTTOM => __(self::SIDEBAR_BOTTOM_LABEL),
		    self::BEFORE_TAB => __(self::BEFORE_TAB_LABEL),
            self::PRODUCT_DETAIL => __(self::PRODUCT_DETAIL_LABEL),
            self::CONTENT_TOP => __(self::CONTENT_TOP_LABEL),
            self::PAGE_BOTTOM => __(self::PAGE_BOTTOM_LABEL),
            //self::MENU_TOP => __(self::MENU_TOP_LABEL),
            //self::MENU_BOTTOM => __(self::MENU_BOTTOM_LABEL),
			self::PAGE_ACCORDION => __(self::PAGE_ACCORDION_LABEL)
        ];
    }

    public function toOptionArray()
    {
        return [
            //['value' => self::SIDEBAR_TOP,  'label' => __(self::SIDEBAR_TOP_LABEL)],
            //['value' => self::SIDEBAR_BOTTOM,  'label' => __(self::SIDEBAR_BOTTOM_LABEL)],
			['value' => self::BEFORE_TAB,  'label' => __(self::BEFORE_TAB_LABEL)],
            ['value' => self::PRODUCT_DETAIL,  'label' => __(self::PRODUCT_DETAIL_LABEL)],
            ['value' => self::CONTENT_TOP,  'label' => __(self::CONTENT_TOP_LABEL)],
            ['value' => self::PAGE_BOTTOM,  'label' => __(self::PAGE_BOTTOM_LABEL)],
            //['value' => self::MENU_TOP,  'label' => __(self::MENU_TOP_LABEL)],
            //['value' => self::MENU_BOTTOM,  'label' => __(self::MENU_BOTTOM_LABEL)],
			['value' => self::PAGE_ACCORDION,  'label' => __(self::PAGE_ACCORDION_LABEL)],
        ];
    }
}