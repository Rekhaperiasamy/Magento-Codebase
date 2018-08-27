<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Controller\Adminhtml\Csblock;

/**
 * Class Delete
 * @package Aheadworks\Csblock\Controller\Adminhtml\Csblock
 */
class Delete extends \Aheadworks\Csblock\Controller\Adminhtml\Csblock
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Aheadworks\Csblock\Model\CsblockFactory
     */
    protected $csblockModelFactory;

    protected $contentCollectionFactory;

    protected $contentModelFactory;



    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory,
        \Aheadworks\Csblock\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Aheadworks\Csblock\Model\ContentFactory $contentModelFactory
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
        $this->csblockModelFactory = $csblockModelFactory;
        $this->contentCollectionFactory = $contentCollectionFactory;
        $this->contentModelFactory = $contentModelFactory;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        /* @var $CsblockModel \Aheadworks\Csblock\Model\Csblock */
        $csblockModel = $this->csblockModelFactory->create();
        if ($id) {
            $csblockModel->load($id);
            if ($csblockModel->getId()) {
                try {
                    /* delete block content */
                    $contentCollection = $this->contentCollectionFactory->create();
                    $contentCollection->addBlockIdFilter($csblockModel->getId());
                    foreach ($contentCollection as $row) {
                        $contentModel = $this->contentModelFactory->create();
                        $contentModel->load($row->getId());
                        $contentModel->delete();
                    }

                    $csblockModel->delete();
                    $this->messageManager->addSuccessMessage(__('Block was successfully deleted.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $resultRedirect->setPath('*/csblock/edit', ['id' => $this->getRequest()->getParam('id')]);
                }
            }
        }
        return $resultRedirect->setPath('*/csblock/');
    }
}
