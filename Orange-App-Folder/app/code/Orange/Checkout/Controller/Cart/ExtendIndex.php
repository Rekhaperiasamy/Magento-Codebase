<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Orange\Checkout\Controller\Cart;

class ExtendIndex extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
		//Start: If cart contain prepaid card only it goes to checkout intermediate page
		$cartData = $this->_checkoutSession->getQuote();
		$totalItemsInCart = $cartData->getItemsCount();
		$productAttributeSet = array();
		if($totalItemsInCart) {
			$isPrice = 0;
			foreach ($cartData->getAllItems() as $item) {
				$productAttributeSet[] = $item->getProduct()->getAttributeSetId();
				if($item->getPrice() >0)
					$isPrice = 1;
			}
			if(count(array_unique($productAttributeSet)) == 1 && $productAttributeSet[0] == 14 && $isPrice == 0) {
				return $this->_redirect('checkout/cart/prepaid-simcard', ['_secure' => true]);
			}
		
		}
		//End: If cart contain prepaid card only it goes to checkout intermediate page
		
		if ($cartData && $cartData->getItemsCount()) {
			$cartData->collectTotals()->save();
		}
		
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Shopping Cart'));
        return $resultPage;
    }
}
