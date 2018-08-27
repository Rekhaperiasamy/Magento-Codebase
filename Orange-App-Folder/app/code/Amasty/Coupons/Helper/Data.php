<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Coupons
 */


namespace Amasty\Coupons\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var Resource
     */
    protected $resource;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $ruleModel;
	protected $ruleLabel;
    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    protected $couponModel;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $session,
        \Magento\SalesRule\Model\Coupon $couponModel,
		\Magento\SalesRule\Model\ResourceModel\Rule $ruleLabel,
		\Magento\SalesRule\Model\RuleRepository $ruleModel,
        \Magento\SalesRule\Model\ResourceModel\Coupon $couponResourceModel,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        parent::__construct($context);
        $this->resource = $resource;
        $this->couponModel = $couponModel;
		$this->ruleModel = $ruleModel;
		$this->ruleLabel = $ruleLabel;
        $this->session = $session;
    }

    public function getRealAppliedCodes()
    {
        $quote = $this->session->getQuote();

        if (!$quote->getCouponCode()) {
            return false;
        }

        $coupons =  array_map('trim', explode(',', $quote->getCouponCode()));
        $appliedRules = array_map('trim', explode(',', $quote->getAppliedRuleIds()));

        if (!$appliedRules) {
            return false;
        }

        foreach ($coupons as $key => $coupon) {
            $rule = $this->couponModel->loadByCode($coupon);
            if (!$this->couponModel->getResource()->exists($coupon) || !in_array($rule->getRuleId(), $appliedRules)) {
                unset($coupons[$key]);
            }
        }

        $coupons = array_unique($coupons);

        return $coupons;
    }
	
	public function getCouponData($couponCode)
	{
		$coupon = $this->couponModel->loadByCode($couponCode);
		return $coupon;
	}
	
	public function getRuleData($ruleId)
	{
		$rule = $this->ruleModel->getById($ruleId);
		return $rule;
	}
	public function getStoreLabel($ruleId, $storeId)
	{
		$storelabel = $this->ruleLabel->getStoreLabel($ruleId, $storeId);
		return $storelabel;
	}

}
