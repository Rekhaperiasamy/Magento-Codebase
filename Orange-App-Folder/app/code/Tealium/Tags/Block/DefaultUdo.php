<?php
namespace Tealium\Tags\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Action\Action;


class DefaultUdo extends Udo
{
  protected $request;
    private $_objectManager;
  private $locale;
  private $storeName;
  private $storeCode;
  private $market;
  private $currency;
  private $pageName;
  protected $_checkoutSession;
  protected $registry;
  protected $_pageTitle;
  protected $_pageConfig;
  protected $region;

  public function __construct(
  \Magento\Framework\App\Request\Http $request,
  \Magento\Checkout\Model\Session $checkoutSession,
  \Magento\Framework\Registry $coreRegistry,
  Context $context, array $data = [])
  {
    $this->request=$request;
    $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $this->_checkoutSession = $checkoutSession;
    $customerType = $this->_objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();
    if($customerType == "SOHO") {
      $this->market = "soho";
    } else {
      $this->market = "b2c";
    }
    $fullAction= $this->request->getFullActionName();
    $moduleName = $this->request->getModuleName();
        $controller =$this->request->getControllerName();
        $action     = $this->request->getActionName();
    // echo "fullAction->".$fullAction."moduleName->".$moduleName."controller->".$controller."action->".$action; die;
        $this->registry=$coreRegistry;
    $pageT= $this->_objectManager->get('Magento\Catalog\Model\Session')->getCustomTitle();
    $resolver = $this->_objectManager->get('Magento\Framework\Locale\Resolver');
    $lang= $resolver->getLocale();
    $langarray   = explode('_',$lang);
    $this->locale = $langarray[0];
    $this->storeName=$context->getStoreManager()->getStore()->getName();
    $this->storeCode=$context->getStoreManager()->getStore()->getCode();
    $this->region="obe";
    $currencysymbol = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
    $this->currency = strtolower($currencysymbol->getStore()->getCurrentCurrencyCode());
    $this->siteSection = "eshop";
    if(!$pageT){
        $pageT=__('Orange');
    }
    $this->pageName =$pageT;
// print_r($fullAction); die;
    $product = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');//get current product
        if($product != '')
      {

      // Normalised accented characters from title.
      $newText = iconv('UTF-8', 'ASCII//TRANSLIT', $product->getName());
      $this->pageName = trim(strtolower(str_replace('`', '', $newText)));
      $this->getProductPage();
      }
      else
      {

        if($fullAction=='checkout_cart_index'){
          $this->getCartPage();
      }elseif($fullAction=='checkout_index_index'){
          $this->getCheckoutPage();
      }elseif($fullAction=='customer_account_index' || $fullAction=='customer_account_edit'){
          $this->getCustomerData();
      }elseif($fullAction=='catalog_category_view'){
             //$this->getCategory();
      }elseif($fullAction=='intermediate_listing_item'){
             //$this->getIntermediatePage();
      }elseif($fullAction=='mnp_index_index'){
             $this->getMnp();
      }elseif($fullAction=='cms_page_view'){
             $this->getActiver();
      }elseif($fullAction=='checkout_onepage_success'||$fullAction=='checkout_index_success'){
          $this->getOrderSuccessPage();
      }elseif($fullAction=='cms_index_index'){
          $this->getHome();
      }elseif($moduleName=='checkout' && $controller=='onepage' && $action=='success' ){
          $this->getOrderSuccessPage();
      }elseif($fullAction=='webform_index_mnp'){
          $this->getWebformDetails();
      }else if ($fullAction == 'webform_index_Success') {
        $this->getActiveOrder();
      }
      else{
            $this->getGenericData();
        }

    }
   parent::__construct($context, $data);
}
  /**
  * Get cart Data
  *
  * @return  Array
  */
  public function getCartPage() {
    $eventType = "";
    $checkout_ids = $checkout_skus = $checkout_names = $checkout_qtys = array();
    $checkout_prices = $checkout_original_prices = $discounts = array();
    $product_updated = $product_added = $product_removed = $checkout_updated = array();
    $totalMonthlyPrice = $totalNextPrice = $totalPrice = "";
    $productCategory = $productType = $productEngagementPeriod = array();
    $productMonthlyPrice = $productBrand = $checkout_added = array();
    $couponUsed = array("");
    $subTotal = 0;

    $eventType = $this->_checkoutSession->getTealiumEventType();
    $addCartItem = $this->_checkoutSession->getAddCartProduct();
    $removeCartItem = $this->_checkoutSession->getRemovedCartItem();

    $cart = $this->_objectManager->get('\Magento\Checkout\Model\Cart');
    $quote = $cart->getQuote();
    $totalItems = $quote->getItemsCount();
    $ruleDiscountAmount = 0;
    if (!empty($quote->getCouponCode())) {
      $couponData = $this->_objectManager->get('\Amasty\Coupons\Block\Coupon');
      $orangeCouponHelper = $this->_objectManager->get('Orange\Coupon\Helper\Data');
      $couponUsed = $couponData->getCouponsCode();
      foreach ($couponUsed as $couponCode) {
        $coupon = $couponData->getCouponData($couponCode);
        $ruleId = $coupon->getRuleId();
        $ruleDiscount = $orangeCouponHelper->getRuleDiscountAmount($ruleId);

        foreach($ruleDiscount as $ruleDsnt) {
          $ruleDiscountAmount += $ruleDsnt->getTotalDiscountAmount() + $ruleDsnt->getTotalSubscriptionDiscountAmount();
        }
      }
    }
    $discounts[] = (string)$ruleDiscountAmount;
    $bundleProduct = array();
    if ($totalItems > 0) {
      // retrieve quote items collection
      $itemsCollection = $cart->getQuote()->getItemsCollection();
      // get array of all items what can be display directly
      $itemsVisible = $cart->getQuote()->getAllVisibleItems();
      // retrieve quote items array
      $items = $cart->getQuote()->getAllItems();
      $childProducts = $productQty = array();
      foreach($items as $item) {
        $cartProduct = $item->getProduct();
        $productAddedToCart = $cartProduct->getSku();
        $productID = $this->_objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($productAddedToCart);

        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productID);
        $productTypeDetails = $product->getTypeId();
        $productAttribute = $product->getAttributeSetID();
        if ($productTypeDetails == 'bundle') {
          $typeInstance = $product->getTypeInstance();
          $requiredChildrenIds = $typeInstance->getChildrenIds($productID, true);
          $childProducts[$productID] = current(array_values($requiredChildrenIds));
          $productQty[$productID] = $item->getQty();
          $bundleProduct[$productID] = number_format($item->getPrice(), 2, ".", "");
        }
        if ($productTypeDetails != 'bundle') {
          // Getting tealium_page_name attribute for each products name.
          $checkout_names[] = $productName = $this->getAttributesValue($product, 'tealium_page_name');
          $productCategoryName = $this->getAttributesValue($product, 'tealium_product_category');
          $productTypeName = $this->getAttributesValue($product, 'tealium_product_type');
          if ($productTypeDetails == 'virtual') {
            $productMonthlyPrice[] = number_format($this->getAttributesValue($product, 'subscription_amount'), 2, ".", "");
            $productEngagementPeriod[] = $this->getAttributesValue($product, 'subsidy_duration');
            $productBrand[] = $this->getAttributesValue($product, 'brand');
            $productCategory[] = $productCategoryName;
            $productType[] = $productTypeName;
          } else if ($productTypeDetails == 'simple' && $productAttribute == '14') {
            $productType[] = $productTypeName;
            $productBrand[] = $this->getAttributesValue($product, 'brand');
            $productCategory[] = $productCategoryName;
            $productMonthlyPrice[] = "0";
            $productEngagementPeriod[] = "0";
          } else {
            $productType[] = $productTypeName;
            $productBrand[] = $this->getAttributesValue($product, 'brand');
            $productCategory[] = $productCategoryName;
            $productMonthlyPrice[] = "0";
            $productEngagementPeriod[] = "0";
          }
          $checkoutQty = 0;
          $listPrice = "0.00";
          if (!empty($childProducts)) {
            foreach ($childProducts as $key => $pIds) {
              if (array_key_exists($productID, $pIds)) {
                $checkoutQty = $productQty[$key];
                $listPrice = $bundleProduct[$key];
              }
            }
          }
          $unitPrice = number_format($item->getProduct()->getPrice(), 2, ".", "");

          $checkout_prices[] = ($listPrice > 0 && $productTypeDetails != 'virtual') ? number_format($listPrice, 2, ".", "") : $unitPrice;
          $checkout_qtys[]= ($checkoutQty > 0) ? $checkoutQty : $item->getQty();
          $checkout_original_prices[] = $unitPrice;
          $checkout_added[] = $item->getSku();
          $checkout_skus[] = $product->getSku();
          $checkout_updated[$item->getProductId()] = $item->getUpdatedAt();
        }
      }

      $product_added = $checkout_added;
      $max =  array_keys($checkout_updated, max($checkout_updated));
      $product_updated[] = (string)$max[0];
      $totalNextPrice = $totalMonthlyPrice = (string)number_format($quote->getSubscriptionTotal(), 2, ".", "");
      $totalPrice = (string)number_format($quote->getGrandTotal(), 2, ".", "");

    }
    $outputArray = array(
      "market" => $this->market,
      "site_region" =>$this->region,
      "site_currency" =>$this->currency,
      "site_language_code"=>$this->storeCode,
      "site_section" => $this->siteSection,
      "page_name" =>"shopping cart",
      "page_type" => "checkout",
      "event_type" => $eventType,
      "product_added"  => $product_updated ? $product_updated : array(),
      "product_removed" => array(),
      "product_category" => $productCategory,
      "product_type" => $productType,
      "product_brand" => $productBrand ? : array(),
      "product_sku"  => $checkout_skus ? : array(),
      "product_name"  => $checkout_names ? : array(),
      "product_engagement_period" => $productEngagementPeriod,
      "product_monthly_price" => $productMonthlyPrice,
      "product_unit_price" => $checkout_original_prices ? : array(),
      "product_list_price" => $checkout_prices,
      "product_quantity" => $checkout_qtys ? : array(),
      "order_discount" =>  $discounts ? : array(),
      "order_discount_id" => $couponUsed ? : array(),
      "total_price" => $totalPrice,
      "total_monthly_price" => $totalMonthlyPrice,
      "total_next_price" => $totalNextPrice,
    );

      if (!$eventType) {
        $outputArray['event_type'] = 'refresh';
        unset($outputArray['product_added']);
        unset($outputArray['product_removed']);
      } else if ($eventType == 'remove') {
        $outputArray['event_type'] = $eventType;
        unset($outputArray['product_added']);
        $outputArray['product_removed'] = (is_array($removeCartItem)) ? $removeCartItem : array($removeCartItem);
        $this->_checkoutSession->unsTealiumEventType();
      } else if ($eventType == 'add') {
        $outputArray['product_added'] = (is_array($addCartItem)) ? $addCartItem : array($addCartItem);
        $outputArray['event_type'] = $eventType;
        unset($outputArray['product_removed']);
        $this->_checkoutSession->unsTealiumEventType();
      }

      $this->merge($outputArray);
  }
  public function getCheckoutPage($requestJS = false) {
    $checkout_ids = $checkout_skus = $checkout_names = $checkout_qtys = array();
    $checkout_prices = $checkout_original_prices = $discounts = array();
    $product_updated = $product_added = $product_removed = $checkout_updated = array();
    $checkout_added = $totalMonthlyPrice = $totalNextPrice = $totalPrice = array();
    $productCategory = $productType = $productEngagementPeriod = array();
    $productMonthlyPrice = $productBrand = array();
    $couponUsed = array("");
    $subTotal = 0;

    $cart = $this->_objectManager->get('\Magento\Checkout\Model\Cart');
    $quote = $cart->getQuote();
    $totalItems = $cart->getQuote()->getItemsCount();
    $ruleDiscountAmount = 0;
    if (!empty($quote->getCouponCode())) {
      $couponData = $this->_objectManager->get('\Amasty\Coupons\Block\Coupon');
      $orangeCouponHelper = $this->_objectManager->get('Orange\Coupon\Helper\Data');
      $couponUsed = $couponData->getCouponsCode();
      foreach ($couponUsed as $couponCode) {
        $coupon = $couponData->getCouponData($couponCode);
        $ruleId = $coupon->getRuleId();
        $ruleDiscount = $orangeCouponHelper->getRuleDiscountAmount($ruleId);

        foreach($ruleDiscount as $ruleDsnt) {
          $ruleDiscountAmount += $ruleDsnt->getTotalDiscountAmount() + $ruleDsnt->getTotalSubscriptionDiscountAmount();
        }
      }
    }
    $discounts[] = (string)$ruleDiscountAmount;
    $bundleProduct = array();
    if($totalItems > 0 ){
      // retrieve quote items collection
      $itemsCollection = $cart->getQuote()->getItemsCollection();
      // get array of all items what can be display directly
      $itemsVisible = $cart->getQuote()->getAllVisibleItems();
      // retrieve quote items array
      $items = $cart->getQuote()->getAllItems();
      $childProducts = $productQty = array();
      foreach($items as $item) {
        $cartProduct = $item->getProduct();
        $productID = $this->_objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($cartProduct->getSku());

        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productID);
        $productTypeDetails = $product->getTypeId();
        $productAttribute = $product->getAttributeSetID();
        if ($productTypeDetails == 'bundle') {
          $typeInstance = $product->getTypeInstance();
          $requiredChildrenIds = $typeInstance->getChildrenIds($productID, true);
          $childProducts[$productID] = current(array_values($requiredChildrenIds));
          $productQty[$productID] = $item->getQty();
          $bundleProduct[$productID] = number_format($item->getPrice(), 2, ".", "");
        }
        if ($productTypeDetails != 'bundle') {
          // Getting tealium_page_name attribute for each products name.
          $checkout_names[] = $productName = $this->getAttributesValue($product, 'tealium_page_name');

          $productCategoryName = $this->getAttributesValue($product, 'tealium_product_category');
          $productTypeName = $this->getAttributesValue($product, 'tealium_product_type');

          if ($productTypeDetails == 'virtual') {
            $productMonthlyPrice[] = number_format($this->getAttributesValue($product, 'subscription_amount'), 2, ".", "");
            $productEngagementPeriod[] = $this->getAttributesValue($product, 'subsidy_duration');
            $productBrand[] = $this->getAttributesValue($product, 'brand');
            $productCategory[] = $productCategoryName;
            $productType[] = $productTypeName;
          } else if ($productTypeDetails == 'simple' && $productAttribute == '14') {
            $productType[] = $productTypeName;
            $productBrand[] = $this->getAttributesValue($product, 'brand');
            $productCategory[] = $productCategoryName;
            $productMonthlyPrice[] = "0";
            $productEngagementPeriod[] = "0";
          } else {
            $productType[] = $productTypeName;
            $productBrand[] = $this->getAttributesValue($product, 'brand');
            $productCategory[] = $productCategoryName;
            $productMonthlyPrice[] = "0";
            $productEngagementPeriod[] = "0";
          }
          $checkoutQty = 0;
          $listPrice = "0.00";
          if (!empty($childProducts)) {
            foreach ($childProducts as $key => $pIds) {
              if (array_key_exists($productID, $pIds)) {
                $checkoutQty = $productQty[$key];
                $listPrice = $bundleProduct[$key];
              }
            }
          }
          $unitPrice = number_format($item->getProduct()->getPrice(), 2, ".", "");

          $checkout_prices[] = ($listPrice > 0 && $productTypeDetails != 'virtual') ? number_format($listPrice, 2, ".", "") : $unitPrice;
          $checkout_qtys[]= ($checkoutQty > 0) ? $checkoutQty : $item->getQty();
          $checkout_original_prices[] = $unitPrice;
          $checkout_skus[] = $product->getSku();
        }
      }
    }
      $totalNextPrice = $totalMonthlyPrice = (string)number_format($quote->getSubscriptionTotal(), 2, ".", "");
      // $totalNextPrice[] = (string)number_format($quote->getSubsidyDiscount(), 2, ".", "");
      $totalPrice = (string)number_format($quote->getGrandTotal(), 2, ".", "");

    $orderSessionValues = $this->_objectManager->get('Magento\Checkout\Model\Session')->getNewcheckout();
    $numberType = array();
    $currentOperator = "";
    if ($orderSessionValues['totalvirtualproduct']) {
      if (strpos($orderSessionValues['totalvirtualproduct'], ',')) {
        $total_cart_products = explode(',', $orderSessionValues['totalvirtualproduct']);
        foreach ($total_cart_products as $cart_productIds) {
          if ($orderSessionValues['design_sim_number-' . $cart_productIds]) {
            $numberType[] = ($orderSessionValues['design_sim_number-' . $cart_productIds] == 1) ? 'existing' : 'new';
            $currentOperator = strtolower($orderSessionValues['subscription_type-' . $cart_productIds]);
          }
        }
      } else {
        $cart_productId = $orderSessionValues['totalvirtualproduct'];
        $numberType[] = ($orderSessionValues['design_sim_number-' . $cart_productId] == 1) ? 'existing' : 'new';
        $currentOperator = strtolower($orderSessionValues['subscription_type-' . $cart_productId]);
      }
    }
    if (!empty($orderSessionValues['coupon_code'])) {
      $couponUsed = explode(',', $orderSessionValues['coupon_code']);
      $discounts = $orderSessionValues['coupon_discount_amount'];
    }

    $outputArray = array(
      "market" => $this->market,
      "site_region" =>$this->region,
      "site_currency" =>$this->currency,
      "site_language_code"=>$this->storeCode,
      "site_section" => $this->siteSection,
       "page_name" =>"",
       "page_type" => "checkout",
       "product_category" => $productCategory,
       "product_type" => $productType,
       "product_brand" => $productBrand ? : array(),
       "product_sku"  => $checkout_skus ? : array(),
       "product_name"  => $checkout_names ? : array(),
       "product_engagement_period" => $productEngagementPeriod,
       "product_monthly_price" => $productMonthlyPrice,
       "product_unit_price" => $checkout_original_prices ? : array(),
       "product_list_price" => $checkout_prices,
       "product_quantity" => $checkout_qtys ? : array(),
       "order_discount" => $discounts ? : array(),
       "order_discount_id" => $couponUsed ? : array(),
       "total_price" => $totalPrice ? : array(),
       "total_monthly_price" => $totalMonthlyPrice,
       "total_next_price" => $totalNextPrice ? : array(),
        "checkout_step"=> "",
        "number_type"=> $numberType,
        "current_operator"=> $currentOperator
      );
    if ($requestJS) {
      return $outputArray;
    }
    $this->merge($outputArray);
  }

  /**
  * Get customer data
  *
  * @return  Array
  */
  public function getCustomerData(){
    $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
    if($customerSession->isLoggedIn()) {
      $customerId=$customerSession->getCustomer()->getId();
      $customerName=$customerSession->getCustomer()->getName();
      $customerGroup=$customerSession->getCustomer()->getGroupId();
      $customerEmail=$customerSession->getCustomer()->getEmail();
      $customerType=$customerSession->getCustomer()->getId();
      $this->merge([
        "site_region" =>$this->region,
                "site_currency" =>$this->currency,
        "site_language_code"=>$this->storeCode,
                "page_name" => $this->pageName,
        "page_type" => "account",
        "customer_id"=>$customerId,
        "customer_email"=>$customerEmail,
        "customer_type"=>$customerType,
        "market" => $this->market

        ]);
    }else{
      $this->getGenericData();
    }

  }

  /**
  * Get order data
  *
  * @return  Array
  */
  /**
  * Get order data
  *
  * @return  Array
  */
  public function getOrderSuccessPage(){
    $checkout_ids = $checkout_skus = $checkout_names = $checkout_qtys = array();
    $checkout_prices = $checkout_original_prices = $discounts = array();
    $product_updated = $product_added = $product_removed = $checkout_updated = array();
    $checkout_added = $totalMonthlyPrice = $totalNextPrice = $totalPrice = array();
    $productCategory = $productType = $productEngagementPeriod = array();
    $productMonthlyPrice = $productBrand = $couponUsed = $numberType = array();
    $subTotal = 0; $currentOperator = "";

    $orderId= $this->registry->registry('OrderId');
    // $orderId='300110403'; // Simple product
    // $orderId='300110393'; // Subsidy product
    $this->_objectManager->get('Psr\Log\LoggerInterface')->debug($orderId);
    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
    $quoteId = $order->getQuoteId();
    $quote = $this->_objectManager->create('Magento\Quote\Model\Quote')->loadByIdWithoutStore($quoteId);
    $bundleProduct = array();
    // retrieve quote items array
    $items = $quote->getAllItems();
    $childProducts = $productQty = array();
    foreach($items as $item) {
      $cartProduct = $item->getProduct();
      $productAddedToCart = $cartProduct->getSku();
      $productID = $this->_objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($productAddedToCart);
      $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productID);
      $productTypeDetails = $product->getTypeId();
      $productAttribute = $product->getAttributeSetID();
      if ($productTypeDetails == 'bundle') {
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($productID, true);
        $childProducts[$productID] = current(array_values($requiredChildrenIds));
        $productQty[$productID] = $item->getQty();
        $bundleProduct[$productID] = number_format($item->getPrice(), 2, ".", "");
      }
      if ($productTypeDetails != 'bundle') {
        // Getting tealium_page_name attribute for each products name.
        $checkout_names[] = $productName = $this->getAttributesValue($product, 'tealium_page_name');
        $productCategoryName = $this->getAttributesValue($product, 'tealium_product_category');
        $productTypeName = $this->getAttributesValue($product, 'tealium_product_type');
        if ($productTypeDetails == 'virtual') {
          $productMonthlyPrice[] = number_format($this->getAttributesValue($product, 'subscription_amount'), 2, ".", "");
          $productEngagementPeriod[] = $this->getAttributesValue($product, 'subsidy_duration');
          $productBrand[] = $this->getAttributesValue($product, 'brand');
          $productCategory[] = $productCategoryName;
          $productType[] = $productTypeName;
        } else if ($productTypeDetails == 'simple' && $productAttribute == '14') {
          $productType[] = $productTypeName;
          $productBrand[] = $this->getAttributesValue($product, 'brand');
          $productCategory[] = $productCategoryName;
          $productMonthlyPrice[] = "0";
          $productEngagementPeriod[] = "0";
        } else {
          $productType[] = $productTypeName;
          $productBrand[] = $this->getAttributesValue($product, 'brand');
          $productCategory[] = $productCategoryName;
          $productMonthlyPrice[] = "0";
          $productEngagementPeriod[] = "0";
        }
        $checkoutQty = 0;
        $listPrice = "0.00";
        if (!empty($childProducts)) {
          foreach ($childProducts as $key => $pIds) {
            if (array_key_exists($productID, $pIds)) {
              $checkoutQty = $productQty[$key];
              $listPrice = $bundleProduct[$key];
            }
          }
        }
        $unitPrice = number_format($item->getProduct()->getPrice(), 2, ".", "");

        $checkout_prices[] = ($listPrice > 0 && $productTypeDetails != 'virtual') ? number_format($listPrice, 2, ".", "") : $unitPrice;

        $checkout_qtys[]= ($checkoutQty > 0) ? $checkoutQty : $item->getQty();
        $checkout_original_prices[] = $unitPrice;

        $checkout_added[] = $item->getSku();
        $checkout_skus[] = $product->getSku();
        $checkout_updated[$item->getProductId()] = $item->getUpdatedAt();
      }
    }
    $totalNextPrice = $totalMonthlyPrice = (string)number_format($quote->getSubscriptionTotal(), 2, ".", "");

    $totalPrice = (string)number_format($quote->getGrandTotal(), 2, ".", "");
    if (!empty($order->getCouponCode())) {
      $couponUsed[] = $order->getCouponCode();
    }
    // Get checkout pages values for current order.
    $model = $this->_objectManager->create('Orange\Abandonexport\Model\Items');
    $abandonexport = $model->getCollection()->addFieldToFilter('quote_id', $quoteId);
    $orderSessionValues = unserialize($abandonexport->getFirstItem()->getStepsecond());

    $numberType = array();
    $currentOperator = "";
    if ($orderSessionValues['totalvirtualproduct']) {
      if (strpos($orderSessionValues['totalvirtualproduct'], ',')) {
        $total_cart_products = explode(',', $orderSessionValues['totalvirtualproduct']);
        foreach ($total_cart_products as $cart_productIds) {
          if ($orderSessionValues['design_sim_number-' . $cart_productIds]) {
            $numberType[] = ($orderSessionValues['design_sim_number-' . $cart_productIds] == 1) ? 'existing' : 'new';
            $currentOperator = strtolower($orderSessionValues['subscription_type-' . $cart_productIds]);
          }
        }
      } else {
        $cart_productId = $orderSessionValues['totalvirtualproduct'];
        $numberType[] = ($orderSessionValues['design_sim_number-' . $cart_productId] == 1) ? 'existing' : 'new';
        $currentOperator = strtolower($orderSessionValues['subscription_type-' . $cart_productId]);
      }
    }
    $discounts = $order->getDiscountAmount();
    if (!empty($orderSessionValues['coupon_code'])) {
      $couponUsed = explode(',', $orderSessionValues['coupon_code']);
      $discounts = $orderSessionValues['coupon_discount_amount'];
    }
    if ($discounts < 0) {
      $discounts = abs($discounts);
    }

    $orderSubtotal = $orderTax = "0.00";
    $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
    $discountSohoPrice = $scopeConfig->getValue('soho/soho_configuration/soho_discount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    $orderGrandTotal = $order->getGrandTotal();
    // Subtotal calculated formula taken from "Orange/Email/Helper/Oracle.php"
    $orderSubtotal = $orderGrandTotal / (1 + ($discountSohoPrice / 100));
    $orderTax = $orderGrandTotal - $orderSubtotal;

    $outputArray = array(
      "market" => $this->market,
      "site_region" =>$this->region,
      "site_currency" =>$this->currency,
      "site_language_code"=>$this->storeCode,
      "site_section" => $this->siteSection,
      "page_name" => "confirmation",
      "page_type" => "checkout",
      "product_category" => $productCategory ? : array(),
      "product_type" => $productType ? : array(),
      "product_brand" => $productBrand ? : array(),
      "product_sku"  => $checkout_skus ? : array(),
      "product_name"  => $checkout_names ? : array(),
      "product_engagement_period" => $productEngagementPeriod ? : array(),
      "product_monthly_price" => $productMonthlyPrice ? : array(),
      "product_unit_price" => $checkout_original_prices ? : array(),
      "product_list_price" => $checkout_prices ? : array(),
      "product_quantity" => $checkout_qtys ? : array(),
      "product_discount" => number_format($discounts, 2, ".", "") ? : array("0"),
      "product_discount_id" => $couponUsed ? : array(),
      "total_price" => $totalPrice ? : array(),
      "total_monthly_price" => $totalMonthlyPrice,
      "total_next_price" => $totalNextPrice ? : array(),
      "checkout_step"=> "confirmation",
      "number_type"=> $numberType,
      "current_operator"=> $currentOperator,
      "order_id" => $order->getIncrementId() ? : "",
      "order_payment_type" => $order->getPayment() ? strtolower($order->getPayment()->getMethodInstance()->getTitle()) : '',
      "order_discount" => number_format($discounts, 2, ".", "") ? : array("0"),
      "order_discount_id" => $couponUsed ? : array(),
      "order_subtotal" => number_format($orderSubtotal, 2, ".", "") ? : "",
      "order_tax" => number_format($orderTax, 2, ".", "") ? : "",
      "order_total" => number_format($orderGrandTotal, 2, ".", "") ? : ""
    );

    $this->merge($outputArray);
  }

  /**
  * Get Home page data
  *
  * @return  Array
  */
     public function getHome(){
     $pageName=__('home');
     $currency=$this->currency;
     $siteRegion=$this->region;
     $siteLanguageCode=$this->storeCode;
       $this->merge([
      "site_region" => $siteRegion,
      "site_currency" => $currency,
      "site_language_code"=>$siteLanguageCode,
      "page_name" => $pageName,
            "page_type" => "home",
      "market" => $this->market
        ]);
  }

  /**
  * Get category page data
  *
  * @return  Array
  */
  public function getCategory(){
    $pageName=$this->pageName;
     $_category = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_category');//get current category
     $siteSection = $this->siteSection;
     $productId = array();
     $productSku = $prices = array();
     $productName = $productEngagementPeriod = $productMonthlyPrice = array();
     $productType = $productCategory = $productBrand = array();
     if($_category){
         $parent = false;
         $grandparent = false;
     if ($_category->getParentId()) {
      $categoryFactory = $this->_objectManager->get('\Magento\Catalog\Model\CategoryFactory');
      $parent = $categoryFactory->create()->load($_category->getParentId());
      if ($parent->getParentId()) {
          $grandparent = $categoryFactory->create()->load($parent->getParentId());
      }
     }
     $productCollections = $this->getProductCollection($_category->getId());
     foreach ($productCollections as $product){
      $productId = $product->getId();
      $productDetails = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($productId);
      $productSku[] = $productDetails->getSku();
      $title = $productDetails->getName();
      $newText = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
      $productName[] = trim(strtolower(str_replace('`', '', $newText)));

      $productTypeDetails = $productDetails->getTypeId();
      $productAttribute = $productDetails->getAttributeSetID();
      if ($productDetails->getTypeId() == 'bundle') {
        $typeInstance = $productDetails->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($productId, true);
        $productCategoryName = $this->getAttributesValue($productDetails, 'tealium_product_category');
        if (strpos($productCategoryName, ',')) {
          $productCategory = explode(',', $productCategoryName);
        }
        $productTypeName = $this->getAttributesValue($product, 'tealium_product_type');
        if (strpos($productTypeName, ',')) {
          $productType = explode(',', $productTypeName);
        }
        foreach ($requiredChildrenIds as $ids) {
          foreach ($ids as $id) {
            $childProducts = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);
            $productSku[] = $sku = $childProducts->getSku();
            $productName[] = strtolower($childProducts->getName());
            $prices[] = number_format($childProducts->getPrice(), 2, ".", "");
            if (is_numeric($sku)) {
              $productBrandName = $this->getAttributesValue($childProducts, 'brand');
              $productBrand[] = $productBrandName;
              $productBrand[] = "nintendo";
              $productListPrice = array(number_format($productDetails->getPrice(), 2), "0");
            }
            if($childProducts->getTypeId() == 'virtual') {
              $productMonthlyPrice[] = !empty($childProducts->getSubscriptionAmount()) ?
                $childProducts->getSubscriptionAmount() : "0";
              $productEngagementPeriod[] = $childProducts->getSubsidyDuration() ?
                $childProducts->getSubsidyDuration() : "0";
            } else {
              $productMonthlyPrice[] = "0";
              $productEngagementPeriod[] = "0";
            }

            if($childProducts->getPrice()) {
              $productUnitPrice[] = number_format($childProducts->getPrice(), 2);
            }
          }
        }
      } else {
        $prices[] = number_format($productDetails->getPrice(), 2, ".", "");
        $currentPageName = $this->getAttributesValue($productDetails, 'page_name');
        $productCategoryName = $this->getAttributesValue($productDetails, 'tealium_product_category');
        $productTypeName = $this->getAttributesValue($productDetails, 'tealium_product_type');
        if ($productTypeDetails == 'virtual') {
          $productBrand[] = strpos($currentPageName, 'zen') ? "zen" : "animal";
          $productCategory[] = $productCategoryName;
          $productType[] = $productTypeName;
          if ($productAttribute == '15') {
            $productBrand = array("iew");
            $productType = array("mobile internet");
          }
          $productMonthlyPrice[] = number_format($productDetails->getSubscriptionAmount(), 2);
          $productEngagementPeriod[] = $productDetails->getSubsidyDuration();
        } else if ($productTypeDetails == 'simple' && $productAttribute == '14') {
          $productType[] = $productTypeName;
          $productBrand[] = "tempo";
          $productCategory[] = $productCategoryName;
          $productMonthlyPrice[] = "0";
          $productEngagementPeriod[] = "0";
        } else {
          $productType[] = $productTypeName;
          $productBrand[] = $this->getAttributesValue($productDetails, 'brand');
          $productCategory[] = $productCategoryName;
          $productMonthlyPrice[] = "0";
          $productEngagementPeriod[] = "0";
        }
      }
     }
     if ($grandparent) {
                $section = $grandparent->getName();
                $category = $parent->getName();
                $subcategory = $_category->getName();
            } elseif ($parent) {
                $section = $parent->getName();
                $category = $_category->getName();
            } else {
                $category = $_category->getName();
            }

     $currency=$this->currency;
     $siteRegion=$this->region;
     $siteLanguageCode=$this->storeCode;
     $this->merge([
      "market" => $this->market,
      "site_region" => $siteRegion,
      "site_currency" => $currency,
      "site_language_code"=> $siteLanguageCode,
      "site_section"=> $siteSection,
      "page_name" =>$pageName,
      "page_type" => "product list",
      "product_category" => $productCategory ? : array(),
      "product_type"=>$productType ? : array(),
      "product_brand"=>$productBrand ? : array(),
      "product_sku"=> $productSku ? : array(),
      "product_name"=> $productName ? : array(),
      "product_engagement_period"=>$productEngagementPeriod ? : array(),
      "product_monthly_price"=>$productMonthlyPrice ? : array(),
      "product_unit_price"=>$prices ? : array(),
        ]);
    }else{
       $this->getGenericData();
    }

  }
  public function getProductCollection($catId){
    $productCollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
    $collection = $productCollection->create();
    $collection->addAttributeToSelect('*');
    $collection->addAttributeToFilter('status', 1);
    $collection->addCategoriesFilter(['eq' => $catId]);
    $collection->load();
    return $collection;
  }
    /**
  * Get Product details page data
  *
  * @return  Array
  */
  public function getProductPage(){
    // Define all required variables.
    $productName = $productSku = $productType = $productBrand = $productCategory =
    $productUnitPrice = $productListPrice = $productMonthlyPrice =
    $productEngagementPeriod = array();
    $pageType = $currentPageName = $productCategoryName = $productTypeName = "";

    //get current product
    $product = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');
    $pageType = "product";
    if($product) {
      if ($product->getTypeId() == 'bundle') {
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($product->getId(), true);
        $childProductPageName = array();
        foreach ($requiredChildrenIds as $ids) {
          foreach ($ids as $id) {
            $childProducts = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($id);

            $productSku[] = $sku = $childProducts->getSku();
            // Getting tealium_page_name attribute for each products name.
            $productName[] = $childProductPageName[] = $this->getAttributesValue($childProducts, 'tealium_page_name');

            $productBrand[] = $this->getAttributesValue($childProducts, 'brand');
            $productType[] = $this->getAttributesValue($childProducts, 'tealium_product_type');

            $childProductCategory = $this->getAttributesValue($childProducts, 'tealium_product_category');
            $productCategory[] = $childProductCategory != 'null' ? $childProductCategory : '';

            if($childProducts->getTypeId() == 'virtual') {
              $productListPrice[] = "0.00";
              $productMonthlyPrice[] = !empty($childProducts->getSubscriptionAmount()) ?
                number_format($childProducts->getSubscriptionAmount(), 2, ".", "") : "0";
              $productEngagementPeriod[] = $childProducts->getSubsidyDuration() ?
                $childProducts->getSubsidyDuration() : "0";
            } else {
              $productListPrice[] = number_format($product->getPrice(), 2, ".", "");
              $productMonthlyPrice[] = "0.00";
              $productEngagementPeriod[] = "0";
            }

            if($childProducts->getPrice()) {
              $productUnitPrice[] = number_format($childProducts->getPrice(), 2, ".", "");
            }
          }
        }
        $currentPageName = count($childProductPageName) > 1 ? implode(' + ', $childProductPageName) : $childProductPageName[0];
      } else {
        // Getting tealium_page_name attribute for each products name.
        $productName[] = $currentPageName = $this->getAttributesValue($product, 'tealium_page_name');

        if ($product->getSku()) {
          $productSku[] = $product->getSku();
        }
        $productAttribute = $product->getAttributeSetID();

        $productCategoryName = $this->getAttributesValue($product, 'tealium_product_category');
        $productTypeName = $this->getAttributesValue($product, 'tealium_product_type');
        $productType[] = $productTypeName;
        $productBrand[] = $this->getAttributesValue($product, 'brand');
        $productCategory[] = $productCategoryName;

        $productMonthlyPrice[] = "0";
        $productEngagementPeriod[] = "0";
        if($product->getPrice()) {
          $productListPrice[] = number_format($product->getPrice(), 2, ".", "");
        }
        if($product->getSpecialPrice()){
          $productUnitPrice[] = number_format($product->getSpecialPrice(), 2, ".", "");
        } else {
          $productUnitPrice = $productListPrice;
        }
      }
    }

    if ($product->getTypeId() == 'bundle') {
      $this->merge([
        "market" => $this->market,
        "site_region" =>$this->region,
        "site_currency" =>$this->currency,
        "site_language_code"=>$this->storeCode,
        "site_section" => $this->siteSection,
        "page_name" => $currentPageName,
        "page_type" => "product",
        "product_category"=>$productCategory,
        "product_type"=>$productType,
        "product_brand"=>$productBrand,
        "product_sku"=>$productSku,
        "product_name"=>$productName,
        "product_engagement_period"=>$productEngagementPeriod,
        "product_monthly_price"=>$productMonthlyPrice,
        "product_unit_price"=>$productUnitPrice,
        "product_list_price"=>$productListPrice
      ]);
    } else {
      $this->merge([
        "market" => $this->market,
        "site_region" =>$this->region,
        "site_currency" =>$this->currency,
        "site_language_code"=>$this->storeCode,
        "site_section" => $this->siteSection,
        "page_name" =>$currentPageName,
        "page_type" => $pageType,
        "product_category"=>$productCategory,
        "product_type"=>$productType,
        "product_brand"=>$productBrand,
        "product_sku"=>$productSku,
        "product_name"=>$productName,
        "product_engagement_period"=>$productEngagementPeriod,
        "product_monthly_price"=>$productMonthlyPrice,
        "product_unit_price"=>$productUnitPrice,
        "product_list_price"=>$productListPrice
      ]);
    }

  }
  public function getIntermediatePage(){
      $intermediateId = $this->request->getParam('id');
      $pageName=$this->pageName;
      $currency=$this->currency;
      $siteRegion=$this->region;
      $siteLanguageCode=$this->storeCode;
      $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($intermediateId);
      //$product = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');//get current product
      $productId = array();
          $productSku = array();
          $productName = array();
          $productType  = array();
        if($product){
      if ($product->getTypeId() == 'bundle') {
        $productType = array("tp","tariff plan");
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($product->getId(), true);
        foreach ($requiredChildrenIds as $ids) {
        foreach ($ids as $id) {
          $childProducts = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);
          $productId[] = $id;
          $productSku[] = $childProducts->getSku();
          $productName[] = $childProducts->getName();
        }
        }
      } else {
        if ($product->getId()) {

          $productId[] =$product->getId() ;
        }
        if ($product->getSku()) {

          $productSku[] = $product->getSku();
        }
        if ($product->getName()) {

          $productName[] = $product->getName();
          }
          $productType = array("tariff plan");
      }
      $pageName=$product->getName();
      }
    $this->merge([
        "site_region" =>$siteRegion,
        "site_currency" =>$currency,
        "site_language_code"=>$siteLanguageCode,
        "page_name" =>$pageName,
        "page_type" => "product",
        "product_id"=>$productId,
        "product_sku"=>$productSku,
        "product_name"=>$productName,
        "product_type"=>$productType,
        "market" => $this->market
      ]);
  }

  /**
  * Get search page data
  *
  * @return  Array
  */
     public function getSearch(){
       //no need to implement search functionality as we are not using magento search
  }

  /**
  * Get search page data
  *
  * @return  Array
  */
     public function getGenericData(){

      $pageName=$this->pageName;
      $currency=$this->currency;
      $siteRegion=$this->region;
      $siteLanguageCode=$this->storeCode;
      $this->merge([
      "site_region" => $siteRegion,
      "site_currency" => $currency,
      "site_language_code"=>$siteLanguageCode,
      "page_name" => $pageName,
      "page_type" => "pages",
      "market" => $this->market
        ]);
  }
  public function getMnp(){
    $title = $this->_objectManager->get('Magento\Framework\View\Page\Title');

    $this->merge([
      "market" => $this->market,
      "site_region" => $this->region,
      "site_currency" => $this->currency,
      "site_language_code"=>$this->storeCode,
      "site_section" => $this->siteSection,
      "page_name" => $title,
      "page_type" => "webform"
    ]);
  }
  public function getActiver() {
      $title = $this->_objectManager->get('Magento\Framework\View\Page\Title');
      $this->merge([
        "market" => $this->market,
        "site_region" => $this->region,
        "site_currency" => $this->currency,
        "site_language_code"=>$this->storeCode,
        "site_section" => $this->siteSection,
        "page_name" => $title,
        "page_type" => "webform",
      ]);
  }

  public function getAttributesValue($product, $attributeName) {
    /* $attributes = array(
      'tealium_page_name' => 'setTealiumPageName',
      'tealium_product_category' => 'setTealiumProductCategory',
      'tealium_product_type' => 'setTealiumProductType',
      'subscription_amount' => 'setSubscriptionAmount',
      'subsidy_duration' => 'setSubsidyDuration',
      'brand' => 'setBrand'
    ); */

     $attribute = $product->getResource()->getAttribute($attributeName);
    if ($attribute) {
      $attribute_value = strtolower($attribute->getFrontend()->getValue($product));
      // $product->$attributes[$attributeName]("");
      return ($attribute_value != 'non' && $attribute_value != 'no') ? $attribute_value : '';
    }
  }

  public function getCategoryDetails($product) {
    if (!$product) {
      return;
    }
    $categoryIds = $product->getCategoryIds();
    $categoryId = $categoryIds[0];
    if (!empty($categoryIds[1])) {
      $categoryId = $categoryIds[1];
    }
    $categoryCollection = $this->_objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
    $categories = $categoryCollection->create()
                  ->addAttributeToSelect('*')
                  ->addAttributeToFilter('entity_id', $categoryId);
    foreach ($categories as $category) {
      return strtolower($category->getName());
    }
  }

  public function getUTagData($pageName = null){

    $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    $customerType = $this->_objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeName();
    $this->udata['market'] = "b2c";
    if($customerType == "SOHO") {
      $this->udata['market'] = "soho";
    }

    $currencysymbol = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
    $this->udata['site_currency'] = strtolower($currencysymbol->getStore()->getCurrentCurrencyCode());

    if($pageName){
      $this->udata['page_name'] = strtolower($pageName);
    }else{
      $_category = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_category');
      if(isset($_category)){
        $this->udata['page_name'] = strtolower($_category->getName());
      }else{
        $this->udata['page_name'] = '';
      }
    }

    $this->udata['site_section'] = "eshop";
    $this->udata['site_region']="obe";
    $this->udata['site_language_code'] = $this->_storeManager->getStore()->getCode();
    $this->udata['page_type'] = 'product_list';
    return $this->udata;
  }

  public function getWebformDetails() {
    $siteSection = $this->siteSection;
    $pageName=$this->pageName;
    $currency=$this->currency;
    $siteRegion=$this->region;
    $siteLanguageCode=$this->storeCode;

    $data = $this->request->getPostValue();
    $this->merge([
      "market" => $this->market,
      "site_region" => $siteRegion,
      "site_currency" => $currency,
      "site_language_code"=>$siteLanguageCode,
      "site_section" => $siteSection,
      "page_name" => strtolower($data['webform_title']),
      "page_type" => "webform",
      "current_operator" => strtolower($data['what_is_your_current_operator_make_your_choice']),
      "card_type" => empty($data['card_type'])? 'prepaid' : 'postpaid'
    ]);

  }

  public function getActiveOrder() {
    $title = $this->_objectManager->get('Magento\Framework\View\Page\Title');

    $catalogSession = $this->_objectManager->get('Magento\Customer\Model\Session');
    $value = $catalogSession->getData('tealium_order_id');
    $title = $catalogSession->getData('tealium_activer_page_title');

    $catalogSession->unsetData('tealium_order_id');
    $catalogSession->unsetData('tealium_activer_page_title');

    $this->merge([
        "market" => $this->market,
        "site_region" => $this->region,
        "site_currency" => $this->currency,
        "site_language_code"=>$this->storeCode,
        "site_section" => $this->siteSection,
        "page_name" => $title,
        "page_type" => "webform",
        "order_id" => $value
      ]);
  }

  /**
   * Get Page data in phtml file.
   * @return json array.
   */
  public function getPageData($pageName = false) {
    if ($pageName) {
      return $this->$pageName(true);
    }
  }

  /**
  * Function to parse title data with UTF-8 chars
  * @return string
  */
  public function getTitleDetails($title)
  {
    return trim(strtolower(str_replace('`', '', iconv('UTF-8', 'ASCII//TRANSLIT', $title))));
  }
}
