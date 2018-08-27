<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block\Adminhtml;

/**
 * Class Menu
 * @package Aheadworks\Csblock\Block\Adminhtml
 */
class Menu extends \Magento\Backend\Block\Template
{
    const ITEM_BLOCK = 'block';
    const ITEM_README = 'readme';
    const ITEM_SUPPORT = 'support';

    protected $_currentItemKey = null;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Aheadworks_Csblock::menu.phtml';

    /**
     * Get menu items
     *
     * @return array
     */
    public function getItems()
    {
        return [
            self::ITEM_BLOCK => [
                'title' => __('Manage Blocks'),
                'url' => $this->getUrl('csblock_admin/csblock/index')
            ],
            self::ITEM_README => [
                'title' => __('Readme'),
                'url' => ' http://confluence.aheadworks.com/display/EUDOC/Custom+Static+Blocks+-+Magento+2',
                'target' => '__blank',
                'class' => 'aw-extensions-menu-separator'
            ],
            self::ITEM_SUPPORT => [
                'title' => __('Get Support'),
                'url' => ' http://ecommerce.aheadworks.com/contacts/',
                'target' => '__blank',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getCurrentItemKey()
    {
        return $this->_currentItemKey;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setCurrentItemKey($key)
    {
        $this->_currentItemKey = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentItemTitle()
    {
        $items = $this->getItems();
        $key = $this->getCurrentItemKey();
        if (!array_key_exists($key, $items)) {
            return '';
        }
        return $items[$key]['title'];
    }
}