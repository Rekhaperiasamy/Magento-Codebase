<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Controller\Adminhtml\Csblock;

/**
 * Class MassDelete
 * @package Aheadworks\Csblock\Controller\Adminhtml\Csblock
 */
class MassDelete extends \Aheadworks\Csblock\Controller\Adminhtml\Csblock
{
    protected $_collection;
    protected $_filter;

    protected $contentCollectionFactory;

    protected $contentModelFactory;

    protected $csblockModelFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory
     * @param \Aheadworks\Csblock\Model\ResourceModel\Csblock\Collection $collection
     * @param \Aheadworks\Csblock\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Aheadworks\Csblock\Model\ContentFactory $contentModelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory,
        \Aheadworks\Csblock\Model\ResourceModel\Csblock\Collection $collection,
        \Aheadworks\Csblock\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Aheadworks\Csblock\Model\ContentFactory $contentModelFactory
    ) {
        parent::__construct($context);
        $this->_collection = $collection;
        $this->_filter = $filter;
        $this->contentCollectionFactory = $contentCollectionFactory;
        $this->contentModelFactory = $contentModelFactory;
        $this->csblockModelFactory = $csblockModelFactory;
    }

    public function execute()
    {
        $this->_collection = $this->_filter->getCollection($this->_collection);
        $count = 0;
        foreach ($this->_collection->getItems() as $csblock) {
            /* delete block content */
            $contentCollection = $this->contentCollectionFactory->create();
            $contentCollection->addBlockIdFilter($csblock->getId());
            foreach ($contentCollection as $row) {
                $contentModel = $this->contentModelFactory->create();
                $contentModel->load($row->getId());
                $contentModel->delete();
            }
            $csblockModel = $this->csblockModelFactory->create();
            $csblockModel->load($csblock->getId());
            if ($csblockModel->getId()) {
                $csblockModel->delete();
                $count++;
            }
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $count)
        );
        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}
