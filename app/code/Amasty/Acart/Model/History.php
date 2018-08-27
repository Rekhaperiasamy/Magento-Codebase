<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class History extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Mail\Template\FactoryInterface
     */
    protected $_templateFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var RuleQuoteFactory
     */
    protected $_ruleQuoteFactory;

    /**
     * @var \Magento\Framework\Mail\MessageFactory
     */
    protected $_messageFactory;

    /**
     * @var \Magento\Framework\Mail\TransportInterfaceFactory
     */
    protected $_mailTransportFactory;

    /**
     * @var
     */
    protected $_store;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    const STATUS_PROCESSING = 'processing';

    const STATUS_SENT = 'sent';

    const STATUS_CANCEL_EVENT = 'cancel_event';

    const STATUS_BLACKLIST = 'blacklist';

    const STATUS_ADMIN = 'admin';

    const STATUS_NOT_NEWSLETTER_SUBSCRIBER = 'not_newsletter_subscriber';

    /**
     * @var \Amasty\Acart\Helper\Data
     */
    protected $helper;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $salesRuleFactory;

    /**
     * @var array
     */
    private $sameCouponData = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection
     */
    private $newsletterSubscriberCollection;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory,
        \Magento\Framework\Mail\Template\FactoryInterface $templateFactory,
        \Magento\Framework\Mail\MessageFactory $messageFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Amasty\Acart\Model\RuleQuoteFactory $ruleQuoteFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        ScopeConfigInterface $scopeConfig,
        \Amasty\Acart\Helper\Data $helper,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\SalesRule\Model\RuleFactory $salesRuleFactory,
        \Amasty\Acart\Model\Config $config,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection $newsletterSubscriberCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_dateTime = $dateTime;
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->_groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_templateFactory = $templateFactory;
        $this->_messageFactory = $messageFactory;
        $this->_mailTransportFactory = $mailTransportFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_quoteFactory = $quoteFactory;
        $this->_ruleQuoteFactory = $ruleQuoteFactory;
        $this->_stockRegistry = $stockRegistry;
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->salesRuleFactory = $salesRuleFactory;
        $this->config = $config;
        $this->newsletterSubscriberCollection = $newsletterSubscriberCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init('Amasty\Acart\Model\ResourceModel\History');
    }

    /**
     * @param int $days
     * @param int $deliveryTime
     * @return null|string
     */
    protected function _getCouponToDate($days, $deliveryTime)
    {
        return $this->_dateTime->formatDate($this->_date->gmtTimestamp() + $days * 24 * 3600 + $deliveryTime);
    }

    /**
     * @param RuleQuote $ruleQuote
     * @param Schedule $schedule
     * @param Rule $rule
     * @param \Magento\Quote\Model\Quote $quote
     * @param $time
     * @return $this
     */
    public function create(
        \Amasty\Acart\Model\RuleQuote $ruleQuote,
        \Amasty\Acart\Model\Schedule $schedule,
        \Amasty\Acart\Model\Rule $rule,
        \Magento\Quote\Model\Quote $quote,
        $time
    ) {
        $couponData = $this->getCouponData($schedule, $rule, $ruleQuote);

        if ($this->helper->isDebugMode()) {
            $deliveryTime = 10;
        } else {
            $deliveryTime = $schedule->getDeliveryTime() ? $schedule->getDeliveryTime() : (5 * 50);
        }

        $this->setData(
            array_merge(
                [
                    'rule_quote_id' => $ruleQuote->getId(),
                    'schedule_id' => $schedule->getId(),
                    'status' => self::STATUS_PROCESSING,
                    'public_key' => uniqid(),
                    'scheduled_at' => $this->_dateTime->formatDate($time + $deliveryTime),
                ],
                $couponData
            )
        );

        $this->save();
        $template = $this->_createEmailTemplate($ruleQuote, $schedule, $rule, clone $quote);

        $this->addData(
            [
                'email_body' => $template->processTemplate(),
                'email_subject' => html_entity_decode($template->getSubject(), ENT_QUOTES),
            ]
        );

        $this->save();

        return $this;
    }

    /**
     * @param Schedule $schedule
     * @param Rule $rule
     * @param RuleQuote $ruleQuote
     * @return array
     */
    private function getCouponData(
        \Amasty\Acart\Model\Schedule $schedule,
        \Amasty\Acart\Model\Rule $rule,
        \Amasty\Acart\Model\RuleQuote $ruleQuote
    ) {
        $couponData = [];
        $salesCoupon = false;
        $salesRule = false;

        if ($schedule->getSendSameCoupon()) {
            $couponData = $this->sameCouponData;
        } else {
            if ($schedule->getUseShoppingCartRule()) {
                $salesRule = $this->salesRuleFactory->create()->load($schedule->getSalesRuleId());
                $salesCoupon = $this->_generateCouponPool($salesRule);
            } elseif ($schedule->getSimpleAction()) {
                $salesRule = $this->_createCoupon($ruleQuote, $schedule, $rule);
            }

            if ($salesRule) {
                $couponData = $this->fillCouponData(
                    $salesRule->getId(),
                    $salesCoupon ? $salesCoupon->getCode() : $salesRule->getCouponCode(),
                    $salesCoupon ? $salesCoupon->getExpirationDate() : $salesRule->getToDate(),
                    $salesCoupon ? $salesCoupon->getId() : null
                );
            }
        }

        if (isset($couponData['sales_rule_coupon']) && (!$this->sameCouponData || !$schedule->getSendSameCoupon())) {
            $this->sameCouponData = $couponData;
        }

        return $couponData;
    }

    /**
     * @param $id
     * @param $coupon
     * @param $couponExpiration
     * @param null $couponId
     * @return array
     */
    private function fillCouponData($id, $coupon, $couponExpiration, $couponId = null)
    {
        return [
            'sales_rule_id' => $id,
            'sales_rule_coupon_id' => $couponId,
            'sales_rule_coupon' => $coupon,
            'sales_rule_coupon_expiration_date' => $couponExpiration,
        ];
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return mixed|null
     */
    protected function _generateCouponPool(\Magento\SalesRule\Model\Rule $rule)
    {
        $salesCoupon = null;

        $generator = $rule->getCouponCodeGenerator();

        $generator = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('Magento\SalesRule\Model\Coupon\Massgenerator');

        $generator->setData(
            [
                'rule_id' => $rule->getId(),
                'qty' => 1,
                'length' => 12,
                'format' => 'alphanum',
                'prefix' => '',
                'suffix' => '',
                'dash' => '0',
                'uses_per_coupon' => '0',
                'uses_per_customer' => '0',
                'to_date' => '',
            ]
        );

        $generator->generatePool();
        $generated = $generator->getGeneratedCount();
        $resourceCoupon = \Magento\Framework\App\ObjectManager::getInstance()
                                            ->create('Magento\SalesRule\Model\ResourceModel\Coupon\Collection');
        $resourceCoupon
            ->addFieldToFilter('main_table.rule_id', $rule->getId())
            ->getSelect()
            ->joinLeft(
                array('h' => $resourceCoupon->getTable('amasty_acart_history')),
                'main_table.coupon_id = h.sales_rule_coupon_id',
                array()
            )->where('h.history_id is null')
            ->order('main_table.coupon_id desc')
            ->limit(1);

        $items = $resourceCoupon->getItems();
        if (count($items) > 0) {
            $salesCoupon = end($items);
        }

        return $salesCoupon;
    }

    protected function _createEmailTemplate(
        \Amasty\Acart\Model\RuleQuote $ruleQuote,
        \Amasty\Acart\Model\Schedule $schedule,
        \Amasty\Acart\Model\Rule $rule,
        \Magento\Quote\Model\Quote $quote
    ) {
        $vars = [
            'quote' => $quote,
            'rule' => $rule,
            'ruleQuote' => $ruleQuote,
            'history' => $this,
            'urlmanager' => \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Amasty\Acart\Model\UrlManager')->init($rule, $this),
            'formatmanager' => \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Amasty\Acart\Model\FormatManager')->init([
                                \Amasty\Acart\Model\FormatManager::TYPE_HISTORY => $this,
                                \Amasty\Acart\Model\FormatManager::TYPE_QUOTE => $quote,
                                \Amasty\Acart\Model\FormatManager::TYPE_RULE_QUOTE => $ruleQuote
                            ]),
        ];

        if ($quote->getCustomerIsGuest()) {
            $vars['customerIsGuest'] = true;
        }

        if ($this->getSalesRuleCoupon()){
            $quote->setCouponCode($this->getSalesRuleCoupon())->collectTotals();
        }

        $template = $this->_templateFactory->get($schedule->getTemplateId())
            ->setVars($vars)
            ->setOptions([
                'area' => Area::AREA_FRONTEND,
                'store' => $ruleQuote->getStoreId()
        ]);

        if ($this->getSalesRuleCoupon()) {
            $quote->setCouponCode("")->collectTotals();
        }

        return $template;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     */
    protected function initDiscountPrices(\Magento\Quote\Model\Quote $quote)
    {
        $this->setSubtotal($quote->getSubtotal());
        $this->setGrandTotal($quote->getGrandTotal());
    }

    /**
     * @param null|int $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }

        if (!$this->_store) {
            $this->_store = $this->_storeManager->getStore($storeId);
        }

        return $this->_store;
    }

    /**
     * @param RuleQuote $ruleQuote
     * @param Schedule $schedule
     * @param Rule $rule
     * @return SalesRule
     */
    protected function _createCoupon(
        \Amasty\Acart\Model\RuleQuote $ruleQuote,
        \Amasty\Acart\Model\Schedule $schedule,
        \Amasty\Acart\Model\Rule $rule
    ) {
        $store = $this->getStore($ruleQuote->getStoreId());
        $salesRule = $this->salesRuleFactory->create();
        $salesRule->setData(
            [
                'name' => 'Amasty: Abandoned Cart Coupon #' . $ruleQuote->getCustomerEmail(),
                'is_active' => '1',
                'website_ids' => [
                    0 => $store->getWebsiteId()
                ],
                'customer_group_ids' => $this->_getGroupsIds($rule),
                'coupon_code' => strtoupper(uniqid()),
                'uses_per_coupon' => 1,
                'coupon_type' => 2,
                'from_date' => '',
                'to_date' => $this->_getCouponToDate($schedule->getExpiredInDays(), $schedule->getDeliveryTime()),
                'uses_per_customer' => 1,
                'simple_action' => $schedule->getSimpleAction(),
                'discount_amount' => $schedule->getDiscountAmount(),
                'stop_rules_processing' => '0',
            ]
        );

        if ($schedule->getDiscountQty() > 0) {
            $salesRule->setDiscountQty($schedule->getDiscountQty());
        }

        if ($schedule->getDiscountStep() > 0) {
            $salesRule->setDiscountStep($schedule->getDiscountStep());
        }

        $salesRule->setConditionsSerialized($this->serializer->serialize($this->_getConditions($rule)));
        $salesRule->save();

        return $salesRule;
    }

    /**
     * @param Rule $rule
     * @return array
     */
    protected function _getGroupsIds(\Amasty\Acart\Model\Rule $rule)
    {
        $groupsIds = [];
        $strGroupIds = $rule->getCustomerGroupIds();

        if (!empty($strGroupIds)) {
            $groupsIds = explode(',', $strGroupIds);
        } else {
            foreach ($this->_groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems() as $group) {
                $groupsIds[] = $group->getId();
            }
        }

        return $groupsIds;

    }

    /**
     * @param Rule $rule
     * @return array
     */
    protected function _getConditions(\Amasty\Acart\Model\Rule $rule)
    {
        $salesRuleConditions = [];
        $conditions = $rule->getSalesRule()->getConditions()->asArray();

        if (isset($conditions['conditions'])) {
            foreach ($conditions['conditions'] as $idx => $condition) {
                if ($condition['attribute'] !== \Amasty\Acart\Model\SalesRule\Condition\Carts::ATTRIBUTE_CARDS_NUM) {
                    $salesRuleConditions[] = $condition;
                }
            }
        }

        return [
            'type'       => 'Magento\SalesRule\Model\Rule\Condition\Combine',
            'attribute' => '',
            'operator' => '',
            'value'      => '1',
            'is_value_processed' => '',
            'aggregator' => 'all',
            'conditions' => $salesRuleConditions
        ];
    }

    /**
     * @param bool $testMode
     */
    public function execute($testMode = false)
    {
        if (!$this->_cancel()) {
            $this->setExecutedAt($this->_dateTime->formatDate($this->_date->gmtTimestamp()))
                ->save();

            if ($testMode) {
                $this->_sendEmail($testMode);
                $status = self::STATUS_SENT;
            } else {
                $blacklist = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('Amasty\Acart\Model\Blacklist')->load($this->getCustomerEmail(), 'customer_email');

                if ($blacklist->getId()) {
                    $status = self::STATUS_BLACKLIST;
                } else if (!$this->validateNewsletterSubscribersOnly($this->getCustomerEmail())) {
                    $status = self::STATUS_NOT_NEWSLETTER_SUBSCRIBER;
                } else {
                    $this->_sendEmail($testMode);
                    $status = self::STATUS_SENT;
                }
            }

            $this->setStatus($status);

            $this->setFinishedAt($this->_dateTime->formatDate($this->_date->gmtTimestamp()))
                ->save();
        } else {
            $this->setStatus(self::STATUS_CANCEL_EVENT)
                ->save();
            $ruleQuote = $this->_ruleQuoteFactory->create()->load($this->getRuleQuoteId());
            $ruleQuote->complete();
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    private function validateNewsletterSubscribersOnly($email)
    {
        if (!$this->config->isEmailsToNewsletterSubscribersOnly($this->getStoreId())) {
            return true;
        }

        /** @var \Magento\Newsletter\Model\Subscriber|null $newsletterSubscriber */
        $newsletterSubscriber = $this->newsletterSubscriberCollection->getItemByColumnValue('subscriber_email', $email);

        if ($newsletterSubscriber && $newsletterSubscriber->getSubscriberStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED) {
            return true;
        }

        return false;
    }


    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return bool|\Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    protected function _getStockItem($quoteItem)
    {
        if (!$quoteItem
            || !$quoteItem->getProductId()
            || !$quoteItem->getQuote()
            || $quoteItem->getQuote()->getIsSuperMode()
        ) {
            return false;
        }

        $stockItem = $this->_stockRegistry->getStockItem(
            $quoteItem->getProduct()->getId(),
            $quoteItem->getProduct()->getStore()->getWebsiteId()
        );

        return $stockItem;
    }

    /**
     * @return bool
     */
    protected function _cancel()
    {
        $cancel = false;

        if ($this->getCancelCondition()) {
            foreach (explode(',', $this->getCancelCondition()) as $cancelCondition) {
                $quote = $this->_quoteFactory->create()->load($this->getQuoteId());
                $quoteValidation = $this->_validateCancelQuote($quote);

                switch ($cancelCondition) {
                    case \Amasty\Acart\Model\Rule::CANCEL_CONDITION_ALL_PRODUCTS_WENT_OUT_OF_STOCK:
                        if (!$quoteValidation['all_products']) {
                            $cancel = true;
                        }
                        break;
                    case \Amasty\Acart\Model\Rule::CANCEL_CONDITION_ANY_PRODUCT_WENT_OUT_OF_STOCK:
                        if (!$quoteValidation['any_products']) {
                            $cancel = true;
                        }
                        break;
                }
            }
        }

        return $cancel;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    protected function _validateCancelQuote($quote)
    {
        $inStock = 0;

        foreach ($quote->getAllItems() as $item) {
            $stockItem = $this->_getStockItem($item);

            if ($stockItem) {
                if ($stockItem->getIsInStock()) {
                    $inStock++;
                }
            }
        }

        return [
            'all_products' => (($inStock == 0) ? false : true),
            'any_products' => (((count($quote->getAllItems()) - $inStock) != 0) ? false : true)
        ];
    }

    /**
     * @param bool $testMode
     */
    protected function _sendEmail($testMode = false)
    {
        $senderName = $this->config->getSenderName($this->getStoreId());
        $senderEmail = $this->config->getSenderEmail($this->getStoreId());
        $bcc = $this->config->getBcc($this->getStoreId());
        $safeMode = $this->config->isSafeMode($this->getStoreId());
        $recipientEmail = $this->config->getTestingRecipientEmail($this->getStoreId());
        $replyToEmail = $this->config->getReplyToEmail($this->getStoreId());
        $replyToName = $this->config->getReplyToName($this->getStoreId());

        $name = [
            $this->getCustomerFirstname(),
            $this->getCustomerLastname(),
        ];

        $to = $this->getCustomerEmail();

        if (($testMode || $safeMode) && $recipientEmail) {
            $to = $recipientEmail;
        }

        $message = $this->_messageFactory->create();

        $message
            ->addTo($to, implode(' ', $name))
            ->setFrom($senderEmail, $senderName)
            ->setMessageType(\Magento\Framework\Mail\MessageInterface::TYPE_HTML)
            ->setBody($this->getEmailBody())
            ->setSubject(html_entity_decode($this->getEmailSubject(), ENT_QUOTES));

        if (!empty($bcc) && !$testMode && !$safeMode) {
            $message->addBcc(explode(',', $bcc));
        }

        if ($replyToEmail) {
            $message->setReplyTo($replyToEmail, $replyToName ?: '');
        }

        $mailTransport = $this->_mailTransportFactory->create(
            [
                'message' => clone $message
            ]
        );
        $mailTransport->sendMessage();
    }

    /**
     * @return Rule
     */
    public function getRule()
    {
        return $this->_ruleQuoteFactory->create()->loadById($this->getRuleQuoteId())->getRule();
    }
}
