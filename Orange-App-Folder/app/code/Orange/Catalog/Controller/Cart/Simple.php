<?php

namespace Orange\Catalog\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;

class Simple extends \Magento\Framework\App\Action\Action {

    /**
     * @var Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    protected $_cart;
    protected $_productRepository;

    public function __construct(
    Context $context, FormKey $formKey, ProductFactory $productFactory, Cart $cart, ProductRepository $productRepository, PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->formKey = $formKey;
        $this->resultPageFactory = $resultPageFactory;
        $this->_productFactory = $productFactory;
        $this->_cart = $cart;
        $this->_productRepository = $productRepository;
    }

    public function execute() {
        try {
            $productId = $this->getRequest()->getParam('id'); //Bundle product id			
            $resultPage = $this->resultPageFactory->create();
            $params = array(
                'form_key' => $this->formKey->getFormKey(),
                'product' => $productId, //product Id
                'qty' => 1, //quantity of product
            );
            $this->_redirect("checkout/cart/add/form_key/", $params);
            return $resultPage;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }

}
