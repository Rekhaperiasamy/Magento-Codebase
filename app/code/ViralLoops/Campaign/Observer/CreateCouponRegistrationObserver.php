<?php
namespace ViralLoops\Campaign\Observer;

use Magento\Framework\Event\ObserverInterface;
use ViralLoops\Campaign\Helper\Data as Helper;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;

class CreateCouponRegistrationObserver implements ObserverInterface
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
     * @param Helper $helper
     * @param SessionManagerInterface $customerSession
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        Helper $helper,
        SessionManagerInterface $customerSession,
        CookieManagerInterface $cookieManager
    ) {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
    }

    /**
     * Create coupon registration
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            if ($this->customerSession->isLoggedIn()) {
                $customer = $this->customerSession->getCustomer();
                $storeId = $customer->getStoreId();
                $data = $this->_prepareDataLoggedInRegistration($customer);
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
    }

    /**
     * Prepare data for LoggedIn customers
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    protected function _prepareDataLoggedInRegistration($customer)
    {
        $email = $customer->getEmail();
        $firstName = $customer->getFirstname();
        $lastName = $customer->getLastname();

        $data = [
            "apiToken" => $this->helper->getApiKey(),
            "params" => [
                "event" => "action",
                "user" => [
                    "firstname" => $firstName ? $firstName : '',
                    "lastname" => $lastName ? $lastName : '',
                    "email" => $email
                ],
                "referrer" => [
                    "referralCode" => $this->cookieManager->getCookie(Helper::VL_REFERRAL_CODE)
                ]
            ]
        ];
        return $data;
    }
}
