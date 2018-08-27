<?php

namespace Orange\Checkout\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
     /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Catalog\Model\Product $product
    * @param Magento\Framework\Data\Form\FormKey $formKey $formkey,
    * @param Magento\Quote\Model\Quote $quote,
    * @param Magento\Customer\Model\CustomerFactory $customerFactory,
    * @param Magento\Sales\Model\Service\OrderService $orderService,
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,       
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService  
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        parent::__construct($context);
    }

    /**
     * Create Order On Your Store
     * 
     * @param array $orderData
     * @return array
     * 
    */
    /*public function createMageOrder($orderData) {
        $store=$this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);// load customet by email address
        if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email']) 
                    ->setPassword($orderData['email']);
            $customer->save();
        }
        $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer

        //add items in quote
        foreach($orderData['items'] as $item){
            $product=$this->_product->load($item['product_id']);
            //$product->setPrice($item['price']);
            $quote->addProduct(
                $product,
                intval($item['qty'])
            );
        }

        //Set Address to quote
        $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);

        // Collect Rates and Set Shipping & Payment Method

        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('freeshipping_freeshipping'); //shipping method
        $quote->setPaymentMethod('checkmo'); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        $quote->save(); //Now Save quote and your quote is ready

        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'checkmo']);

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);

        $order->setEmailSent(0);
        $increment_id = $order->getRealOrderId();
        if($order->getEntityId()){
            $result['order_id']= $order->getRealOrderId();
        }else{
            $result=['error'=>1,'msg'=>'Your custom message'];
        }
        return $result;
    }*/
}
