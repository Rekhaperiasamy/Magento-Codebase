<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Indexer extends \Magento\Framework\DataObject
{
    const LAST_EXECUTED_CODE = 'amasty_acart_last_executed';

    /**
     * @var ResourceModel\Quote\CollectionFactory
     */
    protected $_resourceQuoteFactory;

    /**
     * @var ResourceModel\Rule\CollectionFactory
     */
    protected $_resourceRuleFactory;

    /**
     * @var ResourceModel\History\CollectionFactory
     */
    protected $_resourceHistoryFactory;

    /**
     * @var ResourceModel\RuleQuote\CollectionFactory
     */
    protected $_resourceRuleQuoteFactory;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * @var \Amasty\Acart\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var int
     */
    protected $_actualGap = 600; //2 days

    /**
     * @var null
     */
    protected $_lastExecution = null;

    /**
     * @var null
     */
    protected $_currentExecution  = null;

    /**
     * @var \Amasty\Acart\Model\RuleQuoteFactory
     */
    protected $ruleQuoteFactory;

    /**
     * @var \Amasty\Acart\Model\ResourceModel\RuleQuote\CollectionFactory
     */
    protected $ruleQuoteCollectionFactory;

    /**
     * @var \Magento\Framework\FlagFactory
     */
    private $flagManagerFactory;

    public function __construct(
        \Amasty\Acart\Model\ResourceModel\Quote\CollectionFactory $resourceQuoteFactory,
        \Amasty\Acart\Model\ResourceModel\Rule\CollectionFactory $resourceRuleFactory,
        \Amasty\Acart\Model\ResourceModel\History\CollectionFactory $resourceHistoryFactory,
        \Amasty\Acart\Model\ResourceModel\RuleQuote\CollectionFactory $resourceRuleQuoteFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Amasty\Acart\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        ScopeConfigInterface $scopeConfig,
        \Amasty\Acart\Model\RuleQuoteFactory $ruleQuoteFactory,
        \Amasty\Acart\Model\ResourceModel\RuleQuote\CollectionFactory $ruleQuoteCollectionFactory,
        \Magento\Framework\FlagFactory $flagManagerFactory,
        array $data = []
    ) {
        $this->_resourceQuoteFactory = $resourceQuoteFactory;
        $this->_resourceRuleFactory = $resourceRuleFactory;
        $this->_resourceHistoryFactory = $resourceHistoryFactory;
        $this->_resourceRuleQuoteFactory = $resourceRuleQuoteFactory;
        $this->_resourceConfig = $resourceConfig;
        $this->_dateTime = $dateTime;
        $this->_date = $date;
        $this->_helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->ruleQuoteFactory = $ruleQuoteFactory;
        $this->ruleQuoteCollectionFactory = $ruleQuoteCollectionFactory;
        $this->flagManagerFactory = $flagManagerFactory;
        parent::__construct($data);
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->_prepare();
        $this->_execute();
    }

    /**
     * @return void
     */
    protected function _prepare()
    {
        /** @var \Amasty\Acart\Model\ResourceModel\Quote\Collection $resourceQuote */
        $resourceQuote = $this->_resourceQuoteFactory->create()
            ->addAbandonedCartsFilter()
            ->joinQuoteEmail($this->_helper->isDebugMode(), $this->_helper->getDebugEnabledEmailDomains());

        if (!$this->_helper->isDebugMode()) {
            $resourceQuote->addTimeFilter(
                $this->_dateTime->formatDate($this->_getCurrentExecution()),
                $this->_dateTime->formatDate($this->_getLastExecution())
            );
        }

        if ($this->scopeConfig->getValue('amasty_acart/general/only_customers')) {
            $resourceQuote->addFieldToFilter('main_table.customer_id', ['notnull' => true]);
        }

        /** @var \Amasty\Acart\Model\ResourceModel\Rule\Collection $resourceRule */
        $resourceRule = $this->_resourceRuleFactory->create()
            ->addFieldToFilter('is_active', \Amasty\Acart\Model\Rule::RULE_ACTIVE)
            ->addOrder('priority', \Amasty\Acart\Model\ResourceModel\Quote\Collection::SORT_ORDER_ASC);

        $processedQuotes = [];
        /** @var \Amasty\Acart\Model\Rule $rule */
        foreach ($resourceRule as $rule) {
            /** @var \Magento\Quote\Model\Quote $quote */
            foreach ($resourceQuote as $quote) {
                if (!in_array($quote->getId(), $processedQuotes) && $rule->validate($quote)) {
                    $this->ruleQuoteFactory->create()->createRuleQuote($rule, $quote);
                    $processedQuotes[] = $quote->getId();
                }
            }
        }

        $this->deleteAmbiguousRuleQuotes();
    }

    /**
     * Delete previous rule_quote entities if a setting "send email one time per quote" is disabled.
     *
     * @return void
     */
    protected function deleteAmbiguousRuleQuotes()
    {
        /** @var \Amasty\Acart\Model\ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->ruleQuoteCollectionFactory->create();
        $ruleQuoteCollection->getUniqueQuotes();

        if ($ruleQuoteCollection->getSize()) {
            foreach ($ruleQuoteCollection->getItems() as $item) {
                $item->delete();
            }
        }
    }

    /**
     * _execute
     */
    protected function _execute()
    {
        $resourceHistory = $this->_resourceHistoryFactory->create()
            ->addRuleQuoteData()
            ->addRuleData()
            ->addTimeFilter(
                $this->_dateTime->formatDate($this->_getCurrentExecution()),
                $this->_dateTime->formatDate($this->_getLastExecution())
            )->addFieldToFilter('ruleQuote.status', \Amasty\Acart\Model\RuleQuote::STATUS_PROCESSING);

        foreach ($resourceHistory as $history) {
            $history->execute();
        }

        $resourceRuleQuote = $this->_resourceRuleQuoteFactory->create();

        foreach ($resourceRuleQuote->addCompleteFilter() as $ruleQuote) {
            $ruleQuote->complete();
        }
    }

    /**
     * @return int|null|string
     */
    protected function _getLastExecution()
    {
        if ($this->_lastExecution === null) {
            $flag = $this->createFlag()->loadSelf();
            $this->_lastExecution = (string)$flag->getFlagData();

            if (empty($this->_lastExecution)) {
                $this->_lastExecution = $this->_date->gmtTimestamp() - $this->_actualGap;
            }

            $this->_currentExecution = $this->_date->gmtTimestamp();
            $flag->setFlagData($this->_currentExecution)->save();
        }

        return $this->_lastExecution;
    }

    /**
     * @return \Magento\Framework\Flag
     */
    protected function createFlag()
    {
        return $this->flagManagerFactory->create(['data' => ['flag_code' => self::LAST_EXECUTED_CODE]]);
    }

    /**
     * @return int|null
     */
    protected function _getCurrentExecution()
    {
        return $this->_currentExecution ? $this->_currentExecution : $this->_date->gmtTimestamp();
    }
}
