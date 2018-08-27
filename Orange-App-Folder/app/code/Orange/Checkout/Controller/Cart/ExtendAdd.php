<?php

namespace Orange\Checkout\Controller\Cart;

class ExtendAdd extends \Magento\Checkout\Controller\Cart\Add
{
	// Add drupal prepaid simcard to cart by sku
	public function _getskuproduct(){
		$sku = $this->getRequest()->getParam('sku');
		return $this->productRepository->get($sku);
	}
	
    public function execute()
    {
		$params = $this->getRequest()->getParams();
		if (!array_key_exists("sku",$params)) {
			if (!$this->_formKeyValidator->validate($this->getRequest())) {
				return $this->resultRedirectFactory->create()->setPath('*/*/');
			}
		}
		if (array_key_exists("sku",$params)) {
		   $product=$this->_getskuproduct();
		   $attributeset=$product->getAttributeSetId();
		   if($attributeset=='14'){//Don't allow prepaid product to the cart if SOHO
		        $customerType = $this->_objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();                
                if($customerType == 'SOHO') 
                {
                    $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                    $url = $this->_redirect->getRedirectUrl($cartUrl);
                    return $this->goBack($url);
                }			   
		   }		   
		}

        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
			if (array_key_exists("sku",$params)) {
				$product = $this->_getskuproduct();
			} else {
				$product = $this->_initProduct();
			}
			
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
  

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $cart = $this->cart->getQuote()->getAllVisibleItems();
                    $cartCount = count($cart);
                  //  if ($cartCount > 1) { removed from optimization
                    $store_tl = $this->_storeManager->getStore()->getCode();
                    if ($store_tl == 'nl') {
                   $message = __(
                                                'Je hebt %1 toegevoegd aan je winkelmandje.', $product->getName()
                        );
                    } else {
                        $message = __(
                                                "Vous avez ajouté %1 à votre panier d'achat.", $product->getName()
                        );
                    }
                    $this->messageManager->addSuccessMessage($message);
               // } 
				}
					          $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );
				return $this->goBack(null, $product);
            }
			else
			{
				          $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );
			}
		
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);

        } catch (\Exception $e) {		
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->goBack();
        }
    }
    function checkProductTypeSku($sku){
	
	}
    /**
     * Resolve response
     *
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}
