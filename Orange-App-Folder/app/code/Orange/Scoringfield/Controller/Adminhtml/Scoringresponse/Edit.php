<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */

namespace Orange\Scoringfield\Controller\Adminhtml\Scoringresponse;


class Edit extends \Magento\Backend\App\Action
{
    /* protected $_coreRegistry;
	 
    protected $resultForwardFactory;

    
    protected $resultPageFactory;
	
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    } */
   
   public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Orange\Scoringfield\Model\Scoringresponse');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('scoringfield/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
		$_coreRegistry = $this->_objectManager->create('Magento\Framework\Registry');
        $_coreRegistry->register('scoringfield_scoringresponse', $model);
		$this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('scoringfield_scoringresponse_edit');
        $this->_view->renderLayout();
    }
}
