<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_MageMonkey
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\MageMonkey\Plugin\Newsletter\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Subscriber model
 */
class Subscriber
{
    const STATUS_SUBSCRIBED = 1;
    const STATUS_NOT_ACTIVE = 2;
    const STATUS_UNSUBSCRIBED = 3;
    const STATUS_UNCONFIRMED = 4;

    const XML_PATH_CONFIRMATION_FLAG = 'newsletter/subscription/confirm';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Ebizmarts\MageMonkey\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ebizmarts\MageMonkey\Model\Api|null
     */
    protected $_api = null;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Ebizmarts\MageMonkey\Model\Api $api
     * @param \Ebizmarts\MageMonkey\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        \Ebizmarts\MageMonkey\Model\Api $api,
        \Ebizmarts\MageMonkey\Helper\Data $helper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->_helper = $helper;
        $this->_api = $api;
    }


    /**
     * around plugin for subscribe
     *
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @param \Closure $proceed
     * @param string $email
     * @return int
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function aroundSubscribe(
        \Magento\Newsletter\Model\Subscriber $subject,
        \Closure $proceed,
        $email
    ) {
        $storeId = $this->_storeManager->getStore()->getId();
        if ($this->_helper->isMonkeyEnabled($storeId)) {

            $alreadySubssribed=$subject->loadByEmail($email);

            if($alreadySubssribed->isSubscribed())
            {
                throw new \Exception("You'r email already subscribed.");
            }

            if (!$subject->getId()) {
                $subject->setSubscriberConfirmCode($subject->randomSequence());
            }

            $isConfirmNeed = $this->_scopeConfig->getValue(
                self::XML_PATH_CONFIRMATION_FLAG,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) == 1 ? true : false;
            $isOwnSubscribes = false;

            $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn()
                && $this->_customerSession->getCustomerDataObject()->getEmail() == $email;

            if (!$subject->getId() || $subject->getStatus() == self::STATUS_UNSUBSCRIBED
                || $subject->getStatus() == self::STATUS_NOT_ACTIVE
            ) {
                if ($isConfirmNeed === true) {
                    // if user subscribes own login email - confirmation is not needed
                    $isOwnSubscribes = $isSubscribeOwnEmail;
                    if ($isOwnSubscribes == true) {
                        $subject->setStatus(self::STATUS_SUBSCRIBED);
                    } else {
                        $subject->setStatus(self::STATUS_NOT_ACTIVE);
                    }
                } else {
                    $subject->setStatus(self::STATUS_SUBSCRIBED);
                }
                $subject->setSubscriberEmail($email);
            }

            if ($isSubscribeOwnEmail) {
                try {
                    $customer = $this->customerRepository->getById($this->_customerSession->getCustomerId());
                    $subject->setStoreId($customer->getStoreId());
                    $subject->setCustomerId($customer->getId());
                } catch (NoSuchEntityException $e) {
                    $subject->setStoreId($this->_storeManager->getStore()->getId());
                    $subject->setCustomerId(0);
                }
            } else {
                $subject->setStoreId($this->_storeManager->getStore()->getId());
                $subject->setCustomerId(0);
            }

            $subject->setStatusChanged(true);

            try {
                //added inside a condition since the email address has already been subscribed in
                // Ebizmarts\MageMonkey\Model\Plugin\Subscriber::beforeSubscribe
                // (line: 131: $return = $api->listCreateMember($this->_helper->getDefaultList(), json_encode($data));)
                if ($isSubscribeOwnEmail) {
                    $this->remoteSubscribe($email,$subject);
                }

                if ($isConfirmNeed === true
                    && $isOwnSubscribes === false
                ) {
                    $subject->sendConfirmationRequestEmail();
                } else {
                    $subject->sendConfirmationSuccessEmail();
                }
                $subject->save();

                return $subject->getStatus();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        } else {
            $proceed();
        }
    }

    /**
     * subscribe guest users with mailchimp
     *
     * @param String $email
     * @return void
     */
    protected function remoteSubscribe($email,$subscriber)
    {
        $data = [
            'list_id' => $this->_helper->getDefaultList(),
            'email_address' => $email,
            'email_type' => 'html',
            'status' => 'subscribed'
        ];
        $return=$this->_api->listCreateMember($this->_helper->getDefaultList(), json_encode($data));
        if (isset($return->id)) {
            $subscriber->setMagemonkeyId($return->id);
        }
    }
}
