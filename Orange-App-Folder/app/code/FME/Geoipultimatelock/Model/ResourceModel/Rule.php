<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Stdlib\DateTime;
use \Magento\Framework\Model\ResourceModel\Db\Context;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Store\Model\Store;

class Rule extends AbstractDb
{

    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

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
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        $connectionName = null
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    protected function _construct()
    {
        $this->_init('fme_geoipultimatelock', 'geoipultimatelock_id');
    }

    /**
     * Set store model
     *
     * @param \Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }

    /**
     * Process page data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = array('geoipultimatelock_id = ?' => (int) $object->getId());

        $this->getConnection()->delete($this->getTable('fme_geoipultimatelock_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Assign page to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array) $object->getStores(); //echo '<pre>';print_r($object->getData());exit;
        if (empty($newStores)) {
            $newStores = (array) $object->getStoreId();
        }

        $table = $this->getTable('fme_geoipultimatelock_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array('geoipultimatelock_id = ?' => (int) $object->getId(), 'store_id IN (?)' => $delete);

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array('geoipultimatelock_id' => (int) $object->getId(), 'store_id' => (int) $storeId);
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Page $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int) $object->getStoreId());
            $select->join(
                array('fme_geoipultimatelock_store' => $this->getTable('fme_geoipultimatelock_store')),
                $this->getMainTable() . '.geoipultimatelock_id = fme_geoipultimatelock_store.geoipultimatelock_id',
                array()
            )->where(
                'is_active = ?',
                1
            )->where(
                'fme_geoipultimatelock_store.store_id IN (?)',
                $storeIds
            )->order(
                'fme_geoipultimatelock_store.store_id DESC'
            )->limit(
                1
            );
        }

        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $geoipultimatelockId
     * @return array
     */
    public function lookupStoreIds($geoipultimatelockId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getTable('fme_geoipultimatelock_store'), 'store_id')
            ->where('geoipultimatelock_id = ?', (int) $geoipultimatelockId);

        return $connection->fetchCol($select);
    }
}
