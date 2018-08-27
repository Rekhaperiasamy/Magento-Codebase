<?php
namespace Orange\Checkout\Controller\Cart;

class CouponSubscription extends \Magento\Framework\App\Action\Action
{

	protected $_checkoutSession;
	protected $_objectManager;
	protected $_layoutFactory;
	protected $_orangeCouponHelper;

    public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
		\Orange\Coupon\Helper\Data $orangeCouponHelper
    ) {
		$this->_objectManager 		 = $context->getObjectManager();
		$this->_checkoutSession 	 = $checkoutSession;
		$this->_layoutFactory 	 	 = $layoutFactory;
		$this->_orangeCouponHelper 	 = $orangeCouponHelper;

        parent::__construct($context);
    }
    public function execute()
    {
		$customViewBlock = $this->_layoutFactory->create()->createBlock('Orange\Catalog\Block\Product\CustomView');
		$couponBlock = $this->_layoutFactory->create()->createBlock('Amasty\Coupons\Block\Coupon');
		$couponCodes = $couponBlock->getCouponsCode();
		$customerGroupId = $this->_objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();
		$quoteData = $this->_checkoutSession->getQuote()->getData();
		$html = '';
		$discountAmount = "0";
		foreach ($couponCodes as $couponCode) {
			$coupon = $couponBlock->getCouponData($couponCode);
			$ruleId = $coupon->getRuleId();
			$rule = $couponBlock->getRuleData($ruleId);
			$storeID  = $this->getStoreId();
			$label = $couponBlock->getStoreLabel($ruleId,$storeID);
			$ruleDiscount = $this->_orangeCouponHelper->getRuleDiscountAmount($ruleId);
			$ruleDiscountAmount =0;
			foreach($ruleDiscount as $ruleDsnt){
				$ruleDiscountAmount += $ruleDsnt->getTotalDiscountAmount() + $ruleDsnt->getTotalSubscriptionDiscountAmount();
			}
			$discountAmount = $ruleDiscountAmount;
			$html .= '<div class="well-grey well-grey-padded margin-xs-v-m" style=""><div class="row"><div class="col-xs-12 col-md-8"><h2 class="pull-left margin-xs-b-n margin-xs-t-n margin-xs-r-s"><i class="oi oi-tag margin-xs-t-s"></i></h2><h4>'.$couponCode.'</h4><small>'.$label.'</small><div class="clear"></div></div><div class="col-xs-12 col-md-4 padding-xs-b-s"><span class="new-price orange line-100percent">'.$customViewBlock->getOrangePricingHtml($ruleDiscountAmount, false, false, false, 'pdp total-pricee new-price orange',true).'</span></div></div></div>';
		}
		$html .= '<div class="well-grey well-grey-padded margin-xs-v-m"><div class="row">
		<div class="col-xs-12 col-sm-8 col-md-9">
			<input class="form-control orange-promocode" autocomplete="off" type="text" name="coupon_code" placeholder="'.__('Code promo').'">
		</div>
		<div class="col-xs-12 col-sm-4 col-md-3 margin-xs-t-s margin-sm-t-n">
			<button class="btn btn-primary orange-promocode-submit" name="coupon-submit">'.__('Valider').'</button>
		</div>
	</div></div>';
	if (!empty($couponCodes)) {
		// Capturing Tealium Data
		$couponIds = implode(',', $couponCodes);
		$html .= '<input type="hidden" id="tealium_coupon_code" name="tealium_coupon_code" value=' . $couponIds .' />';
		$html .= '<input type="hidden" id="tealium_coupon_discount" name="tealium_coupon_discount" value=' . $discountAmount .' />';

		$data_fetch = $this->_checkoutSession->getNewcheckout();
		$data_fetch['coupon_code'] = $couponIds;
		$data_fetch['coupon_discount_amount'] = $discountAmount;
		$this->_checkoutSession->setNewcheckout($data_fetch);
	}
		if ($this->getRequest()->isAjax()) {
			if ($customerGroupId == 4) {
				$groupId = 'SOHO';
				$subsPrice = round($quoteData['subscription_total'], 2);
				$priceLabel = $this->_objectManager->create('Orange\Catalog\Model\PriceLabel')->getPriceLabel($groupId);
				$zohoCnt = '<span class="orange line-100percent">('.$subsPrice.' €'.__('/mois').$priceLabel.')</span>';
				$exPrice = round(($quoteData['subscription_total'] / (1-(21/100))), 2);
				$response['subscription_total'] = $customViewBlock->getOrangePricingHtml($exPrice, false, true, true, 'pdp total-pricee new-price orange').$zohoCnt;
			} else {
				$subsPrice = round($quoteData['subscription_total'], 2);
				$response['subscription_total'] = $customViewBlock->getOrangePricingHtml($subsPrice, true, true, true, 'pdp total-pricee new-price orange');
			}
			$response['coupon_line'] = $html;
			$this->getResponse()->representJson(
				$this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
			);
		}
		else {
			return $this->_goBack();
		}
    }

	 public function getStoreId()
	 {
	  $om           = \Magento\Framework\App\ObjectManager::getInstance();
	  $storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
	  $storeID      = $storeManager->getStore()->getId();
	  return $storeID;
	 }

	}
