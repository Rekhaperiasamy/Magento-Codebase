<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Afeature
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Afeature\Controller\Adminhtml\Item;

use Magento\Framework\Controller\ResultFactory;
use Dilmah\Afeature\Controller\Adminhtml\Item;

/**
 * Class Edit
 */
class Edit extends Item
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry         $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->objectManager = $context->getObjectManager();
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('item_id');
        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->objectManager->get('Magento\Backend\Model\Session');
        if ($itemId) {
            try {
                $model = $this->objectManager->create('Dilmah\Afeature\Model\Item')->load($itemId);
                $this->coreRegistry->register('current_item', $model);
                $pageTitle = sprintf('%s', $model->getTitle());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $backendSession->unsRuleData();
                $this->messageManager->addError(__('This item no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

                return $resultRedirect->setPath('dilmah_afeature/*/');
            }
        } else {
            $pageTitle = __('New Banner');
        }

        $breadcrumb = $itemId ? __('Edit Banner') : __('New Banner');
        $resultPage = $this->initResultPage();
        $resultPage->addBreadcrumb($breadcrumb, $breadcrumb);
        $resultPage->getConfig()->getTitle()->prepend(__('Banners'));
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
