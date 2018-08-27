<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Geoipultimatelock::save');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Geoipultimatelock::geoipultimatelock')
                ->addBreadcrumb(__('FME'), __('FME'))
                ->addBreadcrumb(__('Manage Rule'), __('Manage Rule'));
        return $resultPage;
    }

    /**
     * Edit Geoipultimatelock Index
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()
                ->getParam('geoipultimatelock_id');
        $model = $this->_objectManager->create('FME\Geoipultimatelock\Model\Rule');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            
            if (!$model->getId()) {
                $this->messageManager->addError(__('This record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')
                ->getPageData(true);
        
        if (!empty($data)) {
            $model->addData($data);
        }
        
        $model->getConditions()
              ->setJsFormObject('rule_conditions_fieldset');

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('geoipultimatelock_data', $model);
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Rule') : __('New Rule'),
            $id ? __('Edit Rule') : __('New Rule')
        );
        
        $resultPage->getConfig()
                ->getTitle()
                ->prepend(__('Rules'));
        $resultPage->getConfig()
                ->getTitle()
                ->prepend($model->getId() ? $model->getTitle() : __('Geo IP Ultimate Lock'));

        return $resultPage;
    }
}
