<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Csblock\Controller\Adminhtml\Csblock;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;

class MassStatus extends \Aheadworks\Csblock\Controller\Adminhtml\Csblock
{

    protected $_collection;
    protected $_filter;

    protected $csblockModelFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory
     * @param \Aheadworks\Csblock\Model\ResourceModel\Csblock\Collection $collection
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory,
        \Aheadworks\Csblock\Model\ResourceModel\Csblock\Collection $collection
    ) {
        parent::__construct($context);
        $this->_collection = $collection;
        $this->_filter = $filter;
        $this->csblockModelFactory = $csblockModelFactory;
    }

    /**
     * Update product(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->_collection = $this->_filter->getCollection($this->_collection);
        $status = (int) $this->getRequest()->getParam('status');
        $count = 0;

        foreach ($this->_collection->getItems() as $csblock) {
            $csblockModel = $this->csblockModelFactory->create();
            $csblockModel->load($csblock->getId());
            if ($csblockModel->getId()) {
                $csblockModel->setStatus($status);
                $csblockModel->save();
                $count++;
            }
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been updated.', $count)
        );
        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}
