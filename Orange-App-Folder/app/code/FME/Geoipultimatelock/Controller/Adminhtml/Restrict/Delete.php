<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Restrict;

use FME\Geoipultimatelock\Model\RestrictFactory;

class Delete extends \Magento\Backend\App\Action
{
    
    protected $_restrictFactory;
    
    public function __construct(\Magento\Backend\App\Action\Context $context, RestrictFactory $restrictFactory)
    {
        parent::__construct($context);
        
        $this->_restrictFactory = $restrictFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('blocked_id');

        if ($id) {
            /** @var $geoipultimatelockRestrictModel \Fme\Geoipultimatelock\Model\RestrictFactory */
            $geoipultimatelockRestrictModel = $this->_restrictFactory->create();
            $geoipultimatelockRestrictModel->load($id);

            // Check this news exists or not
            if (!$geoipultimatelockRestrictModel->getId()) {
                $this->messageManager->addError(__('This Restricted IP no longer exists.'));
            } else {
                try {
                    // Delete news
                    $geoipultimatelockRestrictModel->delete();
                    $this->messageManager->addSuccess(__('The Restricted IP has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', array('blocked_id' => $geoipultimatelockRestrictModel->getId()));
                }
            }
        }
    }
}
