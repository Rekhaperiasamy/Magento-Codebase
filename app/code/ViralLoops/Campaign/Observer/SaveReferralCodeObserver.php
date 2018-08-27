<?php
namespace ViralLoops\Campaign\Observer;

use Magento\Framework\Event\ObserverInterface;
use ViralLoops\Campaign\Helper\Data as Helper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class SaveReferralCodeObserver implements ObserverInterface
{
    const VL_GUEST_COOKIE  = 'vl_guest_checkout';

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Request instance
     *
     * @var RequestInterface
     */
    protected $request;

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
     * @param RequestInterface $request
     * @param CookieManagerInterface $cookieManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Helper $helper,
        RequestInterface $request,
        CookieManagerInterface $cookieManager,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->cookieManager = $cookieManager;
        $this->storeManager = $storeManager;
    }

    /**
     * Save referral code
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $referralCode = $this->request->getParam('referralCode');
            $email = $this->cookieManager->getCookie(self::VL_GUEST_COOKIE);
            if ($referralCode) {
                $this->cookieManager->setPublicCookie(Helper::VL_REFERRAL_CODE, $referralCode);
            }
            if ($email) {
                $storeId = $this->storeManager->getStore()->getId();
                $data = $this->_prepareDataGuestRegistration($email);
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
                $this->cookieManager->deleteCookie(self::VL_GUEST_COOKIE);
            }
        }
    }

    /**
     * Prepare data for guest customers
     *
     * @param string $email
     * @return array
     */
    protected function _prepareDataGuestRegistration($email)
    {
        $data = [
            "apiToken" => $this->helper->getApiKey(),
            "params" => [
                "event" => "action",
                "user" => [
                    "email" => $email
                ]
            ]
        ];
        return $data;
    }
}
