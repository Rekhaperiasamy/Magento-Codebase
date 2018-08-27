<?php

namespace Orange\Checkout\Controller\Cart;

class ExtendDelete extends \Magento\Checkout\Controller\Cart\Delete
{
    public function execute()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $id = (int)$this->getRequest()->getParam('id');		
		$productData = $objectManager->get('Magento\Quote\Model\Quote\Item')->load($id);
		$objectManager->create('Magento\Checkout\Model\Session')->setRemovedCartItem($productData->getProductId());
        if ($id) {
            try {
                $this->cart->removeItem($id)->save();
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t remove the item.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        $defaultUrl = $this->_objectManager->create('Magento\Framework\UrlInterface')->getUrl('*/*');
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($defaultUrl));
    }
}