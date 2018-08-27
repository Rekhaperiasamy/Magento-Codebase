<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Controller\Adminhtml\Csblock;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Aheadworks\Csblock\Controller\Adminhtml\Csblock
 */
class Edit extends \Aheadworks\Csblock\Controller\Adminhtml\Csblock
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Aheadworks\Csblock\Model\CsblockFactory
     */
    protected $_csblockModelFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory
    ) {
        $this->_coreRegistry = $registry;
        $this->_csblockModelFactory = $csblockModelFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Edit Csblock
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /* @var $csblockModel \Aheadworks\Csblock\Model\Csblock */
        $csblockModel = $this->_csblockModelFactory->create();

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $csblockModel->load($id);
            if (!$csblockModel->getId()) {
                $this->messageManager->addErrorMessage(__('This block no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/*');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $csblockModel->setData($data);
        }
        $this->_coreRegistry->register('aw_csblock_model', $csblockModel);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_Csblock::csblock');
        $resultPage->getConfig()->getTitle()->prepend(
            $csblockModel->getId() ? sprintf("%s \"%s\"", __('Edit Block'), $csblockModel->getName()) : __('New Block')
        );
        $resultPage->getLayout()->getBlock('aw_csblock.menu')->setCurrentItemKey(
            \Aheadworks\Csblock\Block\Adminhtml\Menu::ITEM_BLOCK
        );
        return $resultPage;
    }
}
