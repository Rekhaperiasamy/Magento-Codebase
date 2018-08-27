<?php

namespace Orange\CouponReport\Controller\ImportCoupons;

use Magento\Framework\App\ObjectManager;

class ImportCoupons extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Orange\CouponReport\Helper\Data $helperdatadata,
		\Orange\CouponReport\Model\CouponsFactory $couponFactory,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Orange\Coupon\Model\QuoteCoupon $quoteCoupon
		
		
	) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
		$this->_helperdata = $helperdatadata;
		$this->_orderCollectionFactory = $orderCollectionFactory;
		$this->_couponFactory = $couponFactory;
		$this->_quoteCoupon = $quoteCoupon;
		
		
		
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
		
		$salesorders = $this->_orderCollectionFactory->create()->addFieldToFilter('coupon_code',array('neq' => NULL));

		
		$directCount = 0;
		$fromQUotetable = 0;
		$fromSalesRulestable = 0;
		
		$byFixcount = 0;
		$byCartfixcount = 0;
		$byPercentcount = 0;
		$themostexpencivecount = 0;
		foreach($salesorders as $order)
		{
			 $order_id = $order->getIncrementId();
			 $quote_id = $order->getQuoteId();
			 $status = $order->getStatus();
			 $coupon_code = $order->getCouponCode();
			 $discount_amount = $order->getDiscountAmount();
			 $applied_rule_id = $order->getAppliedRuleIds(); 
			 $subtotal = $order->getSubtotal();
			
			
			$appliedCouponCodes = array_unique(explode(",",$coupon_code)); // Handle duplicate coupon entries in sales order table 
			
			$applied_rule_ids = explode(",",$applied_rule_id);
			
			
			
			if ($applied_rule_id) {
			for ($i=0;$i<count($applied_rule_ids);$i++) {
				$couponFactory = $this->_couponFactory->create();	
				$couponFactory->setOrderId($order_id);
                $couponFactory->setCouponCode($appliedCouponCodes[$i]);
                $couponFactory->setStatus($status);
				$couponFactory->setQuoteId($quote_id);
				if (count($applied_rule_ids) < 2 && $discount_amount != 0)  //Case 1
				{
					$couponFactory->setDiscountAmount(abs($discount_amount)); 
					$directCount++;
				}
				else  //Case 2
				{
					
					$quoteCouponCollection = $this->_quoteCoupon->getCollection()
					->addFieldToFilter('quote_id',$quote_id)            
					->addFieldToFilter('CouponCode',$appliedCouponCodes[$i])
					->getFirstItem();
					if ($quoteCouponCollection->getQuoteCouponId() && !empty($quoteCouponCollection->getQuoteCouponId()))
					{   
						$discount_amount = $quoteCouponCollection->getDiscountAmount() + $quoteCouponCollection->getSubscriptionDiscountAmount();
						$fromQUotetable++;
					} 
					else   //Case 3
					{   
						
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
						$rule = $objectManager->create('Magento\SalesRule\Model\Rule')->load($applied_rule_ids[$i]);
						$ruletype = $rule->getSimpleAction();
						Switch($ruletype)
						{
							case "by_fixed": $discount_amount = $rule->getDiscountAmount(); $byFixcount++; break;
							case "by_percent": $discount_amount = ($subtotal*$rule->getDiscountAmount())/100; $byPercentcount++;    break;
							case "cart_fixed": $discount_amount = $rule->getDiscountAmount(); $byCartfixcount++; break;
							case "themostexpencive": $discount_amount = $subtotal; $themostexpencivecount++; break;
						}
						$fromSalesRulestable++;
					}
					$couponFactory->setDiscountAmount($discount_amount);
					
				}
				$couponFactory->save();
			}	
		}	
			
		}
		echo "Orders->".$salesorders->count();
		echo "<BR><BR>-----------Discount from diff locations-----------";
		echo "<BR>Direct Discount->".$directCount;
		echo "<BR>Discount from Quote table->".$fromQUotetable;
		echo "<BR>Discount from Sales Rules table->".$fromSalesRulestable;
		
		echo "<BR><BR>----------- Rule Types----------<BR>";
		echo "<BR>Fixed->".$byFixcount;
		echo "<BR>Cart Fixed->".$byCartfixcount;
		echo "<BR>Percent->".$byPercentcount;
		echo "<BR>The Most Expecsive->".$themostexpencivecount;
		
		
    }
}
