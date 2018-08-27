<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Dilmah\TeaFaq\Controller\Adminhtml\Category;

/**
 * Class Edit
 *
 * @package Dilmah\TeaFaq\Controller\Adminhtml\Category
 */
class Edit extends Category
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
    protected $_coreRegistry;

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
     * Edit function Controller
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('category_id');

        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');
        if ($categoryId) {
            try {
                $model = $this->_objectManager->create('Dilmah\TeaFaq\Model\Category')->load($categoryId);
                $this->_coreRegistry->register('current_category', $model);
                $pageTitle = __('Edit FAQ Category - ' . sprintf("%s", $model->getTitle()));
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $backendSession->unsRuleData();
                $this->messageManager->addError(__('This category no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath('faq/*/');
            }
        } else {
            $pageTitle = __('New FAQ Category');
        }

        $breadcrumb = $categoryId ? __('Edit FAQ Category') : __('New FAQ Category');
        $resultPage = $this->initResultPage();
        $resultPage->addBreadcrumb($breadcrumb, $breadcrumb);
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Categories'));
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
