<?php 
namespace Orange\CouponReport\Model;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

class Importcoupons extends Command
{
    protected function configure()
    {
        $this->setName('orange:importcoupon')
             ->setDescription('Import Existing Coupons');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       
		ini_set('memory_limit', '512M');
	   //$output->writeln('Success!');
		$obj = \Magento\Framework\App\ObjectManager::getInstance();
		$salesorders = $obj->get('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory')->create()
		->addFieldToFilter('coupon_code',array('neq' => NULL));
		$output->writeln("Orders->".$salesorders->count());
		//echo "Orders->".$salesorders->count();
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
				//$couponFactory = $this->_couponFactory->create();
				$couponFactory = $obj->get('\Orange\CouponReport\Model\CouponsFactory')->create();
				$couponFactory->setOrderId($order_id);
				$couponFactory->setOcrCouponCode($appliedCouponCodes[$i]);
				$couponFactory->setOrderStatus($status);
				$couponFactory->setQuoteId($quote_id);
				if (count($applied_rule_ids) < 2 && $discount_amount != 0)  //Case 1
				{
					$couponFactory->setOcrDiscountAmount(abs($discount_amount)); 
					$directCount++;
				}
				else  //Case 2
				{
					//$quoteCouponCollection = $this->_quoteCoupon->getCollection()
					$quoteCouponCollection = $obj->get('\Orange\Coupon\Model\QuoteCoupon')->getCollection()
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
					$couponFactory->setOcrDiscountAmount($discount_amount);
				}
				$couponFactory->save();
			}	
		}	
			
		}
		$output->writeln( "Orders->".$salesorders->count());
		$output->writeln( "<BR><BR>-----------Discount from diff locations-----------");
		$output->writeln( "<BR>Direct Discount->".$directCount);
		$output->writeln("<BR>Discount from Quote table->".$fromQUotetable);
		$output->writeln( "<BR>Discount from Sales Rules table->".$fromSalesRulestable);
		
		$output->writeln( "<BR><BR>----------- Rule Types----------<BR>");
		$output->writeln( "<BR>Fixed->".$byFixcount);
		$output->writeln( "<BR>Cart Fixed->".$byCartfixcount);
		$output->writeln( "<BR>Percent->".$byPercentcount);
		$output->writeln( "<BR>The Most Expecsive->".$themostexpencivecount);

    }
}

?>