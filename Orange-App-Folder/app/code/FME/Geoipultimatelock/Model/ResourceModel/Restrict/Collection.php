<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Model\ResourceModel\Restrict;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'blocked_id';
    protected function _construct()
    {
        $this->_init(
            'FME\Geoipultimatelock\Model\Restrict',
            'FME\Geoipultimatelock\Model\ResourceModel\Restrict'
        );
    }
    
    public function addStatusFilter($isActive = true)
    {
        
        $this->getSelect()
                ->where('main_table.is_active = ? ', $isActive);
        
        return $this;
    }
    
    public function isIpBlocked($ip)
    {
        
        $this->getSelect()
                ->where('main_table.blocked_ip LIKE (?)', $ip);
        
        return $this;
    }
}
