<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Controller\Adminhtml\Item;

use Magento\Framework\Controller\ResultFactory;
use Dilmah\TeaFaq\Controller\Adminhtml\Item;

/**
 * Class Edit
 *
 * @package Dilmah\TeaFaq\Controller\Adminhtml\Category
 */
class Edit extends Item
{
    /**
     * ObjectManager
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Constructor
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_objectManager = $context->getObjectManager();
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Edit Item Function Controller
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('item_id');
        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');
        if ($itemId) {
            try {
                $model = $this->_objectManager->create('Dilmah\TeaFaq\Model\Item')->load($itemId);
                $this->_coreRegistry->register('current_item', $model);
                $pageTitle = __('Edit FAQ Item - ' . sprintf("%s", $model->getTitle()));
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $backendSession->unsRuleData();
                $this->messageManager->addError(__('This item no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath('faq/*/');
            }
        } else {
            $pageTitle = __('New FAQ Item');
        }

        $breadcrumb = $itemId ? __('Edit FAQ Item') : __('New FAQ Item');
        $resultPage = $this->initResultPage();
        $resultPage->addBreadcrumb($breadcrumb, $breadcrumb);
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Items'));
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
