<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Checkout\Controller\Cart;

class UpdatePost extends \Magento\Checkout\Controller\Cart\UpdatePost
{
    

    /**
     * Update shopping cart data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }	
		$formattedURL = $this->_objectManager->get('Orange\Catalog\Helper\CatalogUrl')->getFormattedUrl($this->_url->getUrl('checkout/cart'));
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setUrl($formattedURL);
		return $resultRedirect;
	}
}
