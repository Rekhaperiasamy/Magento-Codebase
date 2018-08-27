<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model\ResourceModel\History;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Acart\Model\History', 'Amasty\Acart\Model\ResourceModel\History');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @param $currentExecution
     * @param $lastExecution
     * @return $this
     */
    public function addTimeFilter($currentExecution, $lastExecution)
    {
        $this->addFieldToFilter(
            'main_table.scheduled_at',
            [
                'gteq' => $lastExecution
            ]
        )->addFieldToFilter(
            'main_table.scheduled_at',
            [
                'lt' => $currentExecution
            ]
        )->getSelect()
            ->where(
                'main_table.status = ?',
                \Amasty\Acart\Model\History::STATUS_PROCESSING
            );

        return $this;
    }

    /**
     * @param $expiredDate
     * @return $this
     */
    public function addExpiredFilter($expiredDate)
    {
        $this->addFieldToFilter(
            'main_table.finished_at',
            [
                'lt' => $expiredDate
            ]
        )->addFieldToFilter(
            'main_table.status',
            [
                'eq' => \Amasty\Acart\Model\History::STATUS_SENT
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addRuleQuoteData()
    {
        $this->getSelect()
            ->joinLeft(
                ['ruleQuote' => $this->getTable('amasty_acart_rule_quote')],
                'main_table.rule_quote_id = ruleQuote.rule_quote_id',
                ['store_id', 'customer_id', 'customer_email', 'customer_firstname', 'customer_lastname', 'quote_id']
            );

        return $this;
    }

    /**
     * @return $this
     */
    public function addRuleData()
    {
        $this->getSelect()
            ->joinLeft(
                ['rule' => $this->getTable('amasty_acart_rule')],
                'ruleQuote.rule_id = rule.rule_id',
                ['name', 'cancel_condition']
            );

        return $this;
    }

}