<?php

namespace FME\Geoipdefaultstore\Controller\Adminhtml\Geoipdefaultstore;

//use FME\Geoipdefaultstore\Controller\Adminhtml\Geoipdefaultstore;
//extends Geoipdefaultstore
class Delete 
{

    /**
     * @return void
     */
    public function execute()
    {
        $geoipDefaultStoreId = (int) $this->getRequest()->getParam('id');

        if ($geoipDefaultStoreId) {
            /** @var $newsModel \Fme\News\Model\News */
            $geoipDefaultStoreModel = $this->_geoipDefaultStoreFactory->create();
            $geoipDefaultStoreModel->load($geoipDefaultStoreId);

            // Check this news exists or not
            if (!$geoipDefaultStoreModel->getId()) {
                $this->messageManager->addError(__('This Geo-IP Default Store rule no longer exists.'));
            } else {
                try {
                    // Delete news
                    $geoipDefaultStoreModel->delete();
                    $this->messageManager->addSuccess(__('The Geo-IP Default Store rule has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', array('id' => $geoipDefaultStoreModel->getId()));
                }
            }
        }
    }
}
