<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */
namespace Orange\Catalogversion\Model;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\CatalogversionException;

/**
 * Catalogversiontab catalogversion model
 */
class Catalogdraft extends AbstractModel
{

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
	protected function _construct()
    {
        $this->_init(\Orange\Catalogversion\Model\ResourceModel\Catalogdraft::class);
    }
}