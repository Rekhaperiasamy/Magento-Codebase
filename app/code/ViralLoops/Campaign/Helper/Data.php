<?php
namespace ViralLoops\Campaign\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\RuleFactory;
use Magento\SalesRule\Model\ResourceModel\Rule as RuleResource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED          = 'campaign/settings/enabled';
    const XML_PATH_API_KEY          = 'campaign/settings/api_key';
    const XML_PATH_API_CAMPAIGN_ID  = 'campaign/settings/api_campaign_id';
    const XML_PATH_WIDGET_POSITION  = 'campaign/settings/widget_position';
    const API_URL_EVENTS            = 'https://app.viral-loops.com/api/v2/events';
    const VL_REFERRAL_CODE          = 'vl_referral_code';

    /**
     * @var ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var RuleResource
     */
    protected $ruleResource;

    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Construct
     *
     * @param Context $context
     * @param ZendClientFactory $httpClientFactory
     * @param JsonHelper $jsonHelper
     * @param GroupManagementInterface $groupManagement
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleFactory $ruleFactory
     * @param RuleResource $ruleResource
     * @param Coupon $coupon
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ZendClientFactory $httpClientFactory,
        JsonHelper $jsonHelper,
        GroupManagementInterface $groupManagement,
        RuleRepositoryInterface $ruleRepository,
        RuleFactory $ruleFactory,
        RuleResource $ruleResource,
        Coupon $coupon,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->httpClientFactory = $httpClientFactory;
        $this->jsonHelper = $jsonHelper;
        $this->groupManagement = $groupManagement;
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->ruleResource = $ruleResource;
        $this->coupon = $coupon;
        $this->storeManager = $storeManager;
    }

    /**
     * Check is enabled Module
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get API campaign id
     *
     * @return string
     */
    public function getApiCampaignId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_CAMPAIGN_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get widget position
     *
     * @return int
     */
    public function getWidgetPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_WIDGET_POSITION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Send request
     *
     * @param string $url
     * @param array $data
     * @return null | array
     */
    public function sendRequest($url, $data = [])
    {
        $result = null;
        try {
            $httpClient = $this->httpClientFactory->create();
            $httpClient->setUri($url);
            $httpClient->setMethod(\Zend_Http_Client::POST);
            $httpClient->setHeaders(\Zend_Http_Client::CONTENT_TYPE, 'application/json');
            $httpClient->setParameterPost($data);
            $response = $httpClient->request();

            $result = $this->jsonHelper->jsonDecode($response->getBody());
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
            $result = null;
        }

        return $result;
    }

    /**
     * Create coupon code
     *
     * @param string $id
     * @param string $name
     * @param int $amount
     * @param string $type
     * @param int $storeId
     */
    public function createCoupon($id, $name, $amount, $type, $storeId)
    {
        try {
            $defaultGroupId = $this->groupManagement->getDefaultGroup($storeId)->getId();
            $websiteId = $this->storeManager->getWebsite()->getId();

            $salesRule = $this->ruleFactory->create();
            $salesRule->setProductIds(null)
                ->setName($id)
                ->setDescription(null)
                ->setIsActive('1')
                ->setWebsiteIds([$websiteId])
                ->setCustomerGroupIds([0, $defaultGroupId])
                ->setCouponType(Rule::COUPON_TYPE_SPECIFIC)
                ->setCouponCode($name)
                ->setUsesPerCoupon('1')
                ->setUsesPerCustomer('1')
                ->setFromDate(null)
                ->setToDate(null)
                ->setSortOrder(null)
                ->setIsRss('0')
                ->setStopRulesProcessing('0')
                ->setSimpleAction($type == 'percent' ? Rule::BY_PERCENT_ACTION : Rule::CART_FIXED_ACTION)
                ->setDiscountAmount($type == 'percent' ? min(100, $amount) : $amount)
                ->setDiscountQty('0')
                ->setDiscountStep(null)
                ->setSimpleFreeShipping('0')
                ->setApplyToShipping('0')
                ->setStoreLabels([]);

            $this->ruleResource->save($salesRule);
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }

    /**
     * Get rewardId from coupon
     *
     * @param string $couponCode
     * @return string | bool
     */
    public function getRewardIdFromCoupon($couponCode)
    {
        $couponCode = $this->coupon->loadByCode($couponCode);
        try {
            $rule = $this->ruleRepository->getById($couponCode->getRuleId());
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
        $ruleName = $rule->getName();
        if (strpos($ruleName, 'reward_') !== false) {
            return $ruleName;
        }
        return false;
    }
}
