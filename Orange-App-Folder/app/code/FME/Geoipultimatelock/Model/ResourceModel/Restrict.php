<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Stdlib\DateTime;
use \Magento\Framework\Model\ResourceModel\Db\Context;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Store\Model\Store;

class Restrict extends AbstractDb
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        $connectionName = null
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context, $connectionName);
        $this->dateTime = $dateTime;
    }

    protected function _construct()
    {
        $this->_init('fme_geoipultimatelock_restrict', 'blocked_id');
    }
}
