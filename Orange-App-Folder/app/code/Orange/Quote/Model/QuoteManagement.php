<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Orange\Quote\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Model\Quote as QuoteEntity;
use Magento\Quote\Model\Quote\Address\ToOrder as ToOrderConverter;
use Magento\Quote\Model\Quote\Address\ToOrderAddress as ToOrderAddressConverter;
use Magento\Quote\Model\Quote\Item\ToOrderItem as ToOrderItemConverter;
use Magento\Quote\Model\Quote\Payment\ToOrderPayment as ToOrderPaymentConverter;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory as OrderFactory;
use Magento\Sales\Api\OrderManagementInterface as OrderManagement;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteValidator;
use Magento\Quote\Model\CustomerManagement;
use Magento\Framework\App\ObjectManager;

/**
 * Class QuoteManagement
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class QuoteManagement extends \Magento\Quote\Model\QuoteManagement
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var QuoteValidator
     */
    protected $quoteValidator;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var OrderManagement
     */
    protected $orderManagement;

    /**
     * @var CustomerManagement
     */
    protected $customerManagement;

    /**
     * @var ToOrderConverter
     */
    protected $quoteAddressToOrder;

    /**
     * @var ToOrderAddressConverter
     */
    protected $quoteAddressToOrderAddress;

    /**
     * @var ToOrderItemConverter
     */
    protected $quoteItemToOrderItem;

    /**
     * @var ToOrderPaymentConverter
     */
    protected $quotePaymentToOrderPayment;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerModelFactory;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    protected $quoteAddressFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param EventManager $eventManager
     * @param QuoteValidator $quoteValidator
     * @param OrderFactory $orderFactory
     * @param OrderManagement $orderManagement
     * @param CustomerManagement $customerManagement
     * @param ToOrderConverter $quoteAddressToOrder
     * @param ToOrderAddressConverter $quoteAddressToOrderAddress
     * @param ToOrderItemConverter $quoteItemToOrderItem
     * @param ToOrderPaymentConverter $quotePaymentToOrderPayment
     * @param UserContextInterface $userContext
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerModelFactory
     * @param \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param QuoteFactory $quoteFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EventManager $eventManager,
        QuoteValidator $quoteValidator,
        OrderFactory $orderFactory,
        OrderManagement $orderManagement,
        CustomerManagement $customerManagement,
        ToOrderConverter $quoteAddressToOrder,
        ToOrderAddressConverter $quoteAddressToOrderAddress,
        ToOrderItemConverter $quoteItemToOrderItem,
        ToOrderPaymentConverter $quotePaymentToOrderPayment,
        UserContextInterface $userContext,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerModelFactory,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->eventManager = $eventManager;
        $this->quoteValidator = $quoteValidator;
        $this->orderFactory = $orderFactory;
        $this->orderManagement = $orderManagement;
        $this->customerManagement = $customerManagement;
        $this->quoteAddressToOrder = $quoteAddressToOrder;
        $this->quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
        $this->quoteItemToOrderItem = $quoteItemToOrderItem;
        $this->quotePaymentToOrderPayment = $quotePaymentToOrderPayment;
        $this->userContext = $userContext;
        $this->quoteRepository = $quoteRepository;
        $this->customerRepository = $customerRepository;
        $this->customerModelFactory = $customerModelFactory;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->accountManagement = $accountManagement;
        $this->customerSession = $customerSession;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createEmptyCart()
    {
        $storeId = $this->storeManager->getStore()->getStoreId();
        $quote = $this->createAnonymousCart($storeId);

        $quote->setBillingAddress($this->quoteAddressFactory->create());
        $quote->setShippingAddress($this->quoteAddressFactory->create());

        try {
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Cannot create quote'));
        }
        return $quote->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function createEmptyCartForCustomer($customerId)
    {
        $storeId = $this->storeManager->getStore()->getStoreId();
        $quote = $this->createCustomerCart($customerId, $storeId);

        try {
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Cannot create quote'));
        }
        return $quote->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function assignCustomer($cartId, $customerId, $storeId)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $customer = $this->customerRepository->getById($customerId);
        $customerModel = $this->customerModelFactory->create();

        if (!in_array($storeId, $customerModel->load($customerId)->getSharedStoreIds())) {
            throw new StateException(
                __('Cannot assign customer to the given cart. The cart belongs to different store.')
            );
        }
        if ($quote->getCustomerId()) {
            throw new StateException(
                __('Cannot assign customer to the given cart. The cart is not anonymous.')
            );
        }
        try {
            $this->quoteRepository->getForCustomer($customerId);
            throw new StateException(
                __('Cannot assign customer to the given cart. Customer already has active cart.')
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {

        }

        $quote->setCustomer($customer);
        $quote->setCustomerIsGuest(0);
        $quoteIdMaskFactory = $this->getQuoteIdMaskFactory();
        /** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
        $quoteIdMask = $quoteIdMaskFactory->create()->load($cartId, 'quote_id');
        if ($quoteIdMask->getId()) {
            $quoteIdMask->delete();
        }
        $this->quoteRepository->save($quote);
        return true;

    }

    /**
     * Creates an anonymous cart.
     *
     * @param int $storeId
     * @return \Magento\Quote\Model\Quote Cart object.
     */
    protected function createAnonymousCart($storeId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->setStoreId($storeId);
        return $quote;
    }

    /**
     * Creates a cart for the currently logged-in customer.
     *
     * @param int $customerId
     * @param int $storeId
     * @return \Magento\Quote\Model\Quote Cart object.
     * @throws CouldNotSaveException The cart could not be created.
     */
    protected function createCustomerCart($customerId, $storeId)
    {
        $customer = $this->customerRepository->getById($customerId);

        try {
            $quote = $this->quoteRepository->getActiveForCustomer($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteFactory->create();
            $quote->setStoreId($storeId);
            $quote->setCustomer($customer);
            $quote->setCustomerIsGuest(0);
        }
        return $quote;
    }

    /**
     * {@inheritdoc}
     */
    public function placeOrder($cartId, PaymentInterface $paymentMethod = null)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteItems = $quote->getAllVisibleItems();
        if (count($quoteItems)) {
			/** total Qty based on Attribute set ID */
			$attr = array();
			foreach($quoteItems as $item){
				$attributeSetid = $item->getProduct()->getAttributeSetId();
				$productType = $item->getProduct()->getTypeId();
				if($productType=="bundle"){
					foreach ($item->getChildren() as $childrenItem){
						$childAttributesetId = $childrenItem->getProduct()->getAttributeSetId();
						if (($childrenItem->getProductType() == 'virtual')&&($childAttributesetId==11)){
							$attributeSetid = 17; 
							if (array_key_exists($attributeSetid, $attr)) {
								$attr[$attributeSetid] = $attr[$attributeSetid] + $item->getQty(); 
							} else {
								$attr[$attributeSetid] = $item->getQty();
							}
						} elseif (($childrenItem->getProductType() == 'virtual')&&($childAttributesetId==15)){
							$attributeSetid = 16;
							if (array_key_exists($attributeSetid, $attr)){
								$attr[$attributeSetid] = $attr[$attributeSetid] + $item->getQty();
							} else {
								$attr[$attributeSetid] = $item->getQty();				  
							}				  
						}
					}
				} else {
					if (array_key_exists($attributeSetid, $attr)) {
						$attr[$attributeSetid] = $attr[$attributeSetid] + $item->getQty();
					} else {
						$attr[$attributeSetid] = $item->getQty();
					}
				}
			}
			
			foreach ($quoteItems as $item) {
				$attributeSetid = $item->getProduct()->getAttributeSetId();
				$product = $item->getProduct();
				$typeId = $product->getTypeId();
				$childrenItems = $item->getChildren();
				if($typeId=="bundle"){
					foreach ($childrenItems as $childrenItem){
					$childAttributesetId = $childrenItem->getProduct()->getAttributeSetId();
						if (($childrenItem->getProductType() == 'virtual')&&($childAttributesetId==11)){
							$attributeSetid = 17;
							$collections = $om->create('Orange\Limitqty\Model\Limitqty')->getCollection()->addFieldToFilter('customer_group',17)->getFirstItem();
							$qty = $collections->getLimitquantity();
							$totalQty = $attr[$attributeSetid];
							if (($qty != "") && ($totalQty > $qty) && ($frontName == "checkout") && ($controlName == "index")) {
								throw new LocalizedException(__('Unable to place order. Please try again later.'));
							}
						} elseif (($childrenItem->getProductType() == 'virtual')&&($childAttributesetId==15)){
							$attributeSetid = 16;
							$collections = $om->create('Orange\Limitqty\Model\Limitqty')->getCollection()->addFieldToFilter('customer_group',16)->getFirstItem();
							$qty = $collections->getLimitquantity();
							$totalQty = $attr[$attributeSetid];
							if (($qty != "") && ($totalQty > $qty) && ($frontName == "checkout") && ($controlName == "index")) {
								throw new LocalizedException(__('Unable to place order. Please try again later.'));
							}
					   }
					}
				} else {
					$limitData = $om->create('Orange\Limitqty\Model\Limitqty')->getCollection()->addFieldToFilter('customer_group', $product->getAttributeSetId())->getFirstItem();						
					$totalQty = $attr[$attributeSetid];
					$qty = $limitData->getLimitquantity();
					if (($qty != "") && ($totalQty > $qty) && ($frontName == "checkout") && ($controlName == "index")) {
						throw new LocalizedException(__('Unable to place order. Please try again later.'));
					}
				}
			}
		}


        if ($paymentMethod) {
            $paymentMethod->setChecks([
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL,
            ]);
            $quote->getPayment()->setQuote($quote);

            $data = $paymentMethod->getData();
            $quote->getPayment()->importData($data);
        }

        if ($quote->getCheckoutMethod() === self::METHOD_GUEST) {
            $quote->setCustomerId(null);
            $quote->setCustomerEmail($quote->getBillingAddress()->getEmail());
            $quote->setCustomerIsGuest(true);
            //$quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
        }
        //throw new LocalizedException(__('Unable to place order. Please try again later.'));
        $this->eventManager->dispatch('checkout_submit_before', ['quote' => $quote]);

        $order = $this->submit($quote);

        if (null == $order) {
            throw new LocalizedException(__('Unable to place order. Please try again later.'));
        }

        $this->checkoutSession->setLastQuoteId($quote->getId());
        $this->checkoutSession->setLastSuccessQuoteId($quote->getId());
        $this->checkoutSession->setLastOrderId($order->getId());
        $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
        $this->checkoutSession->setLastOrderStatus($order->getStatus());

        $this->eventManager->dispatch('checkout_submit_all_after', ['order' => $order, 'quote' => $quote]);
        return $order->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getCartForCustomer($customerId)
    {
        return $this->quoteRepository->getActiveForCustomer($customerId);
    }

    /**
     * Submit quote
     *
     * @param Quote $quote
     * @param array $orderData
     * @return \Magento\Framework\Model\AbstractExtensibleModel|\Magento\Sales\Api\Data\OrderInterface|object|null
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function submit(QuoteEntity $quote, $orderData = [])
    {
        if (!$quote->getAllVisibleItems()) {
            $quote->setIsActive(false);
            return null;
        }

        return $this->submitQuote($quote, $orderData);
    }

    /**
     * @param Quote $quote
     * @return array
     */
    protected function resolveItems(QuoteEntity $quote)
    {
        $quoteItems = [];
        foreach ($quote->getAllItems() as $quoteItem) {
            /** @var \Magento\Quote\Model\ResourceModel\Quote\Item $quoteItem */
            $quoteItems[$quoteItem->getId()] = $quoteItem;
        }
        $orderItems = [];
        foreach ($quoteItems as $quoteItem) {
            $parentItem = (isset($orderItems[$quoteItem->getParentItemId()])) ?
                $orderItems[$quoteItem->getParentItemId()] : null;
            $orderItems[$quoteItem->getId()] =
                $this->quoteItemToOrderItem->convert($quoteItem, ['parent_item' => $parentItem]);
        }
        return array_values($orderItems);
    }

    /**
     * Submit quote
     *
     * @param Quote $quote
     * @param array $orderData
     * @return \Magento\Framework\Model\AbstractExtensibleModel|\Magento\Sales\Api\Data\OrderInterface|object
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function submitQuote(QuoteEntity $quote, $orderData = [])
    {
        $order = $this->orderFactory->create();
		$this->_objectManager = ObjectManager::getInstance();
		$model = $this->_objectManager->create('Orange\Abandonexport\Model\Items');
		$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$quote->getId());
		$tempSession = unserialize($abandonexport->getFirstItem()->getStepsecond());
		$this->_customergroups = $this->_objectManager->get('\Magento\Customer\Model\Customer\Attribute\Source\Group')->getAllOptions();
		$key = array_search($tempSession['customer_type'], array_column($this->_customergroups, 'label'));
		$customerGroupId = $this->_customergroups[$key]['value'];
		//$this->_objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/orderafterplace.log','helllo'.$tempSession['customer_type'].'iddd->'.$customerGroupId);
		
        $this->quoteValidator->validateBeforeSubmit($quote);
        if (!$quote->getCustomerIsGuest()) {
            if ($quote->getCustomerId()) {
                $this->_prepareCustomerQuote($quote);
            }
            $this->customerManagement->populateCustomerInfo($quote);
        }
        $addresses = [];
        $quote->reserveOrderId();
        if ($quote->isVirtual()) {
            $this->dataObjectHelper->mergeDataObjects(
                '\Magento\Sales\Api\Data\OrderInterface',
                $order,
                $this->quoteAddressToOrder->convert($quote->getBillingAddress(), $orderData)
            );
        } else {
            $this->dataObjectHelper->mergeDataObjects(
                '\Magento\Sales\Api\Data\OrderInterface',
                $order,
                $this->quoteAddressToOrder->convert($quote->getShippingAddress(), $orderData)
            );
			//Start: Added for sales_convert_order purpose
			//$order = $this->quoteAddressToOrder->convert($quote->getBillingAddress(), $orderData);
			//End: Added for sales_convert_order purpose
            $shippingAddress = $this->quoteAddressToOrderAddress->convert(
                $quote->getShippingAddress(),
                [
                    'address_type' => 'shipping',
                    'email' => $quote->getCustomerEmail()
                ]
            );
            $addresses[] = $shippingAddress;
            $order->setShippingAddress($shippingAddress);
            $order->setShippingMethod($quote->getShippingAddress()->getShippingMethod());
        }
        $billingAddress = $this->quoteAddressToOrderAddress->convert(
            $quote->getBillingAddress(),
            [
                'address_type' => 'billing',
                'email' => $quote->getCustomerEmail()
            ]
        );
        $addresses[] = $billingAddress;
        $order->setBillingAddress($billingAddress);
        $order->setAddresses($addresses);
        $order->setPayment($this->quotePaymentToOrderPayment->convert($quote->getPayment()));
        $order->setItems($this->resolveItems($quote));
        if ($quote->getCustomer()) {
            $order->setCustomerId($quote->getCustomer()->getId());
        }
        $order->setQuoteId($quote->getId());
		//$order->setCustomerGroupId($quote->getCustomerGroupId());
		$order->setCustomerGroupId($customerGroupId);
        $order->setCustomerEmail($quote->getCustomerEmail());
        $order->setCustomerFirstname($quote->getCustomerFirstname());
        $order->setCustomerMiddlename($quote->getCustomerMiddlename());
        $order->setCustomerLastname($quote->getCustomerLastname());
        $order->setSubscriptionTotal($quote->getSubscriptionTotal());
		$order->setSubsidyDiscount($quote->getSubsidyDiscount());
		$order->setOriSubscriptionTotal($quote->getOriSubscriptionTotal());
		$order->setCouponDescription($quote->getCouponDescription());
		$order->setAccountNumber($quote->getAccountNumber());
		$order->setDateOfBirth($quote->getDateOfBirth());
       
        $this->eventManager->dispatch(
            'sales_model_service_quote_submit_before',
            [
                'order' => $order,
                'quote' => $quote
            ]
        );
        try {
            $order = $this->orderManagement->place($order);
            $quote->setIsActive(false);
            $this->eventManager->dispatch(
                'sales_model_service_quote_submit_success',
                [
                    'order' => $order,
                    'quote' => $quote
                ]
            );
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $this->eventManager->dispatch(
                'sales_model_service_quote_submit_failure',
                [
                    'order'     => $order,
                    'quote'     => $quote,
                    'exception' => $e
                ]
            );
            throw $e;
        }
        return $order;
    }

    /**
     * Prepare quote for customer order submit
     *
     * @param Quote $quote
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareCustomerQuote($quote)
    {
        /** @var Quote $quote */
        $billing = $quote->getBillingAddress();
        $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->customerRepository->getById($quote->getCustomerId());
        $hasDefaultBilling = (bool)$customer->getDefaultBilling();
        $hasDefaultShipping = (bool)$customer->getDefaultShipping();

        if ($shipping && !$shipping->getSameAsBilling()
            && (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())
        ) {
            $shippingAddress = $shipping->exportCustomerAddress();
            if (!$hasDefaultShipping) {
                //Make provided address as default shipping address
                $shippingAddress->setIsDefaultShipping(true);
                $hasDefaultShipping = true;
            }
            $quote->addCustomerAddress($shippingAddress);
            $shipping->setCustomerAddressData($shippingAddress);
        }

        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $billingAddress = $billing->exportCustomerAddress();
            if (!$hasDefaultBilling) {
                //Make provided address as default shipping address
                if (!$hasDefaultShipping) {
                    //Make provided address as default shipping address
                    $billingAddress->setIsDefaultShipping(true);
                }
                $billingAddress->setIsDefaultBilling(true);
            }
            $quote->addCustomerAddress($billingAddress);
            $billing->setCustomerAddressData($billingAddress);
        }
        if ($shipping && !$shipping->getCustomerId() && !$hasDefaultBilling) {
            $shipping->setIsDefaultBilling(true);
        }
    }

    /**
     * @return \Magento\Quote\Model\QuoteIdMaskFactory
     * @deprecated
     */
    private function getQuoteIdMaskFactory()
    {
        if (!$this->quoteIdMaskFactory) {
            $this->quoteIdMaskFactory = ObjectManager::getInstance()
                ->get(\Magento\Quote\Model\QuoteIdMaskFactory::class);
        }
        return $this->quoteIdMaskFactory;
    }
}
