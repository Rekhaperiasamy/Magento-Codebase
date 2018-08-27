<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Block\Adminhtml\Restrict\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('restrict_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Restrict IPs Information'));
    }
}
