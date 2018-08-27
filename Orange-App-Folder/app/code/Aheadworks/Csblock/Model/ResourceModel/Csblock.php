<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\ResourceModel;

/**
 * Class Csblock
 * @package Aheadworks\Csblock\Model\ResourceModel
 */
class Csblock extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $contentModelFactory;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Aheadworks\Csblock\Model\ContentFactory $contentModelFactory,
        $connectionName = null
    ) {
        $this->contentModelFactory = $contentModelFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_csblock_block', 'id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->hasData('customer_groups') && is_array($object->getData('customer_groups'))) {
            $object->setData('customer_groups', implode(',', $object->getData('customer_groups')));
        }

        if ($object->hasData('time_from') && is_array($object->getData('time_from'))) {
            $object->setData('time_from', implode(',', $object->getData('time_from')));
        }
        if ($object->hasData('time_to') && is_array($object->getData('time_to'))) {
            $object->setData('time_to', implode(',', $object->getData('time_to')));
        }
        if (is_array($object->getData('csblock_conditions'))) {
            $object->setData('product_condition', serialize($object->getData('csblock_conditions')));
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getData('content')) {
            $this->_saveBlockContent($object->getData('content'), $object->getId(), $object->isObjectNew());
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $object->setData('customer_groups', explode(',', $object->getData('customer_groups')));
            $conditions = unserialize($object->getData('product_condition'));
            $object->setData('conditions', $conditions);
            if ($conditions) {
                $object->getRuleModel()->getConditions()->loadArray($conditions, 'csblock');
            }
        }
        return parent::_afterLoad($object);
    }

    protected function _saveBlockContent($data, $csblockId, $isNewObject)
    {
        foreach ($data['id'] as $key => $id) {
            $contentModel = $this->contentModelFactory->create();
            if ($id && !$isNewObject) {
                $contentModel->load($id);
            }
            $contentModel->setCsblockId($csblockId);
            $contentModel->setStoreId($data['store_id'][$key]);
            $contentModel->setStaticBlockId($data['static_block_id'][$key]);
            $contentModel->save();
        }

        return $this;
    }
}