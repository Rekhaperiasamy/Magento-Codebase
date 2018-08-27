<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Afeature
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Afeature\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Item.
 */
class Item extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Item constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     * @param null                                              $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->date = $date;
    }

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('dilmah_afeature', 'item_id');
    }

    /**
     * Perform operations after object load.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
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
     * Get store ids to which specified item is assigned.
     *
     * @param int $itemId
     *
     * @return array
     */
    public function lookupStoreIds($itemId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('dilmah_afeature_store'),
            'store_id'
        )->where(
            'item_id = ?',
            (int) $itemId
        );

        return $connection->fetchCol($select);
    }

    /**
     * Perform operations before object save.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getId()) {
            $object->setDateCreated($this->date->gmtDate());
        }
        $object->setDateUpdated($this->date->gmtDate());

        return $this;
    }

    /**
     * Assign page to store views.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();

        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array) $object->getStores();
        if (empty($newStores)) {
            $newStores = (array) $object->getStoreId();
        }
        $table = $this->getTable('dilmah_afeature_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['item_id = ?' => (int) $object->getId(), 'store_id IN (?)' => $delete];

            $connection->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['item_id' => (int) $object->getId(), 'store_id' => (int) $storeId];
            }

            $connection->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }
}
