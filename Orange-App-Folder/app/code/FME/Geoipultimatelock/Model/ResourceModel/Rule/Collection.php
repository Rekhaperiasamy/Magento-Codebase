<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FME\Geoipultimatelock\Model\ResourceModel\Rule;

//use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'geoipultimatelock_id';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init(
            'FME\Geoipultimatelock\Model\Rule',
            'FME\Geoipultimatelock\Model\ResourceModel\Rule'
        );
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     * @return $this
     * @api
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr(serialize(array('attribute' => $attributeCode)), 5, -1));
        $this->addFieldToFilter('conditions_serialized', array('like' => $match));

        return $this;
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = array($store->getId());
        }

        $this->getSelect()
                ->join(
                    array('store_table' => $this->getTable('fme_geoipultimatelock_store')),
                    'main_table.geoipultimatelock_id = store_table.geoipultimatelock_id',
                    array()
                )
                ->where('store_table.store_id in (?)', array(0, $store))
                ->group('main_table.geoipultimatelock_id');

        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('geoipultimatelock_id');
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(array('cps' => $this->getTable('fme_geoipultimatelock_store')))
                    ->where('cps.geoipultimatelock_id IN (?)', $items);
            $result = $connection->fetchPairs($select);
            if ($result) {
                foreach ($this as $item) {
                    $pageId = $item->getData('geoipultimatelock_id');
                    if (!isset($result[$pageId])) {
                        continue;
                    }

                    if ($result[$pageId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData('geoipultimatelock_id')];
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }

                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', array($result[$pageId]));
                }
            }
        }

        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('fme_geoipultimatelock_store')),
                'main_table.geoipultimatelock_id = store_table.geoipultimatelock_id',
                array()
            )->group(
                'main_table.geoipultimatelock_id'
            );
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Add collection filters by identifiers
     *
     * @param mixed $ruleId
     * @param boolean $exclude
     * @return $this
     */
    public function addIdFilter($ruleId, $exclude = false)
    {
//        if (empty($ruleId)) {
//            $this->_setIsLoaded(true);
//            return $this;
//        }
        if (is_array($ruleId)) {
            if (!empty($ruleId)) {
                if ($exclude) {
                    $condition = array('nin' => $ruleId);
                } else {
                    $condition = array('in' => $ruleId);
                }
            } else {
                $condition = '';
            }
        } else {
            if ($exclude) {
                $condition = array('neq' => $ruleId);
            } else {
                $condition = $ruleId;
            }
        }

        $this->addFieldToFilter('geoipultimatelock_id', $ruleId);
        return $this;
    }

    public function addCountryFilter($country)
    {
        $this->getSelect()
                ->where('main_table.countries_list LIKE \'%' . $country . '%\' ');

        return $this;
    }

    public function addStatusFilter($isActive = true)
    {

        $this->getSelect()
                ->where('main_table.is_active = ? ', $isActive);

        return $this;
    }

    public function addPriorityFilter($dir = 'ASC')
    {

        $this->getSelect()
                ->order('main_table.priority ' . $dir);

        return $this;
    }

    public function addLimit($limit = 1)
    {

        $this->getSelect()
                ->limit($limit);

        return $this;
    }

    public function addPageFilter($pageId)
    {
        $this->getSelect()
                ->where('FIND_IN_SET(?, main_table.cms_page_ids)', $pageId);
        return $this;
    }
}
