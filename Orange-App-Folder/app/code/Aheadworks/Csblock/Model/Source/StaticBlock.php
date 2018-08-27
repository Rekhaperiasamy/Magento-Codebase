<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\Source;

use Magento\Framework\Module\Manager as ModuleManager;

class StaticBlock implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var unknown
     */
    protected $collectionFactory;

    /**
     * @param ModuleManager $moduleManager
     * @param \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ModuleManager $moduleManager,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $collectionFactory
    ) {
        $this->moduleManager = $moduleManager;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->moduleManager->isEnabled('Magento_Cms')) {
            return [];
        }

        $blocksOptions = [];

        $blockCollection = $this->collectionFactory->create();
        return $blockCollection->toOptionArray();
    }
}