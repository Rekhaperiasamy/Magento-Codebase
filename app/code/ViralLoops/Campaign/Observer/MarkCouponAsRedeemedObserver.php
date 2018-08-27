<?php
namespace ViralLoops\Campaign\Observer;

use Magento\Framework\Event\ObserverInterface;
use ViralLoops\Campaign\Helper\Data as Helper;

class MarkCouponAsRedeemedObserver implements ObserverInterface
{
    const API_URL_REWARDED = 'https://app.viral-loops.com/api/v2/rewarded';

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Mark coupon as redeemed
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $order = $observer->getEvent()->getOrder();

            if (!$order) {
                return $this;
            }

            if ($couponCode = $order->getCouponCode()) {
                $rewardId = $this->helper->getRewardIdFromCoupon($couponCode);
                if ($rewardId) {
                    $data = $this->_prepareDataRedeem($rewardId);
                    $this->helper->sendRequest(self::API_URL_REWARDED, $data);
                }
            }
        }
    }

    /**
     * Prepare data
     *
     * @param string $rewardId
     * @return array
     */
    protected function _prepareDataRedeem($rewardId)
    {
        $data = [
            "apiToken" => $this->helper->getApiKey(),
            "rewardId" => $rewardId
        ];
        return $data;
    }
}
