<?php
/**
 * Copyright © 2015 Orange. All rights reserved.
 */

namespace Orange\Abandonexport\Controller\Adminhtml;

use Magento\Framework\App\ObjectManager;
/**
 * Items controller
 */
abstract class Items extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    protected $_productCollectionFactory;
	protected $_orderCollectionFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
		$this->_productCollectionFactory = $productCollectionFactory;
		$this->_orderCollectionFactory = $orderCollectionFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
	    
        $this->_view->loadLayout();
        $this->_setActiveMenu('Orange_Abandonexport::items')->_addBreadcrumb(__('Items'), __('Items'));
        return $this;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Orange_Abandonexport::items');
    }
}
