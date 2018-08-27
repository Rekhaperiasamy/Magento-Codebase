<?php
namespace Orange\Coupon\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class Validator extends \Magento\SalesRule\Model\Validator
{
	 /**
     * Calculate quote totals for each rule and save results
     *
     * @param mixed $items
     * @param Address $address
     * @return $this
     */
    public function initTotals($items, Address $address)
    {
        $address->setCartFixedRules([]);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get('Psr\Log\LoggerInterface')->addDebug('INIT TOTALS'); 
        $objectManager->get('Psr\Log\LoggerInterface')->addDebug($address->getQuote()->getId()); 
        $objectManager->get('Orange\Coupon\Model\QuoteCoupon')->clearQuoteDiscountData($address->getQuote()->getId()); //Clear individual coupon discount


        if (!$items) {
            return $this;
        }

        /** @var \Magento\SalesRule\Model\Rule $rule */
        foreach ($this->_getRules($address) as $rule) {
            if (\Magento\SalesRule\Model\Rule::CART_FIXED_ACTION == $rule->getSimpleAction()
                && $this->validatorUtility->canProcessRule($rule, $address)
            ) {
                $ruleTotalItemsPrice = 0;
                $ruleTotalBaseItemsPrice = 0;
                $validItemsCount = 0;

                foreach ($items as $item) {
                    //Skipping child items to avoid double calculations
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if (!$rule->getActions()->validate($item)) {
                        continue;
                    }
                    if (!$this->canApplyDiscount($item)) {
                        continue;
                    }
                    $qty = $this->validatorUtility->getItemQty($item, $rule);
                    $ruleTotalItemsPrice += $this->getItemPrice($item) * $qty;
                    $ruleTotalBaseItemsPrice += $this->getItemBasePrice($item) * $qty;
                    $validItemsCount++;
                }

                $this->_rulesItemTotals[$rule->getId()] = [
                    'items_price' => $ruleTotalItemsPrice,
                    'base_items_price' => $ruleTotalBaseItemsPrice,
                    'items_count' => $validItemsCount,
                ];
            }
        }

        return $this;
    }
}