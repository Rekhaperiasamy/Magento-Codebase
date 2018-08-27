<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use FME\Geoipultimatelock\Helper\Data;

class Countries extends \Magento\Backend\Block\Template implements TabInterface
{

    protected $_geoiopultimatelockHelper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \FME\Geoipultimatelock\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        array $data = array()
    ) 
{ 
     
     
     
     
     
     
     
     
     
    

        $this->_geoiopultimatelockHelper = $helper;
        $this->_coreRegistry = $registry;

        
        parent::__construct($context, $data);
    }
    
    public function _construct()
    {
        parent::_construct();
        //$this->setTemplate('geoipdefaultstore/edit/form/tab/countries.phtml');
        $this->setUseAjax(true);
    }
    public function getHelper()
    {
        return $this->_geoiopultimatelockHelper;
    }
 
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Countries');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Countries');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->_urlBuilder->getUrl('geoipultimatelock/*/countries', array('_current' => true));
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
