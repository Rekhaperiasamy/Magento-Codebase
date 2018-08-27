<?php
namespace ViralLoops\Campaign\Observer;

use Magento\Framework\Event\ObserverInterface;
use ViralLoops\Campaign\Helper\Data as Helper;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class CreateCouponConversionObserver implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var SessionManagerInterface
     */
    protected $customerSession;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Helper $helper
     * @param SessionManagerInterface $customerSession
     * @param CookieManagerInterface $cookieManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Helper $helper,
        SessionManagerInterface $customerSession,
        CookieManagerInterface $cookieManager,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->storeManager = $storeManager;
    }

    /**
     * Create coupon conversion
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $order = $observer->getEvent()->getOrder();
            if ($this->customerSession->isLoggedIn() && $this->cookieManager->getCookie(Helper::VL_REFERRAL_CODE)) {
                $customer = $this->customerSession->getCustomer();
                $storeId = $customer->getStoreId();
                $data = $this->_prepareDataLoggedInConversion($order, $customer);
            } else {
                $storeId = $this->storeManager->getStore()->getId();
                $data = $this->_prepareDataGuestConversion($order);
            }
            if (!empty($data)) {
                $result = $this->helper->sendRequest(Helper::API_URL_EVENTS, $data);
                if ($result && !empty($result['rewards'])) {
                    foreach ($result['rewards'] as $reward) {
                        if (!empty($reward['coupon'])) {
                            $this->helper->createCoupon(
                                $reward['id'],
                                $reward['coupon']['name'],
                                $reward['coupon']['value'],
                                $reward['coupon']['type'],
                                $storeId
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Prepare data LoggedIn conversion
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    protected function _prepareDataLoggedInConversion($order, $customer)
    {
        $email = $customer->getEmail();

        $data = [
            'apiToken' => $this->helper->getApiKey(),
            'amount' => $order->getBaseSubtotal(),
            'params' => [
                'event' => 'order',
                'user' => [
                    'email' => $email
                ]
            ]
        ];
        return $data;
    }

    /**
     * Prepare data Guest conversion
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    protected function _prepareDataGuestConversion($order)
    {
        $data = [];
        if ($couponCode = $order->getCouponCode()) {
            $rewardId = $this->helper->getRewardIdFromCoupon($couponCode);
            if ($rewardId) {
                $data = [
                    'apiToken' => $this->helper->getApiKey(),
                    'amount' => $order->getBaseSubtotal(),
                    'params' => [
                        'event' => 'order',
                        'user' => [
                            'rewardId' => $rewardId
                        ]
                    ]
                ];
            }
        }
        return $data;
    }
}
