<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Block\Adminhtml\Rule\Edit;

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
        $this->setId('rule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rule Information'));
    }

    protected function _beforeToHtml()
    {

        $this->addTab(
            'country_section', array(
            'label' => 'Countries',
            //'content' => $this->getLayout()->createBlock('FME\Geoipdefaultstore\Block\Adminhtml\Geoipdefaultstore\Edit\Tab\Countries', 'country_section', ['template' => 'FME_Geoipdefaultstore::edit/form/tab/countries.phtml'])->toHtml(),
            'url' => $this->_urlBuilder->getUrl('*/*/countries', array('_current' => true)),
            'class' => 'ajax',
            )
        );

        parent::_beforeToHtml();
    }
}
