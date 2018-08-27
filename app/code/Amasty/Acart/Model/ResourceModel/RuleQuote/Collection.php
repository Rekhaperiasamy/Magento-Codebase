<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */


namespace Amasty\Acart\Model\ResourceModel\RuleQuote;

use Amasty\Acart\Model\RuleQuote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Acart\Model\RuleQuote', 'Amasty\Acart\Model\ResourceModel\RuleQuote');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function addCompleteFilter()
    {
        $this
            ->getSelect()
                ->joinLeft(
                    ['history' => $this->getTable('amasty_acart_history')],
                    'main_table.rule_quote_id = history.rule_quote_id and history.status <> "' . \Amasty\Acart\Model\History::STATUS_SENT . '"',
                    []
                )
                ->where('main_table.status = ? ' , \Amasty\Acart\Model\RuleQuote::STATUS_PROCESSING)
                ->group('main_table.rule_quote_id')
                ->having('count(history.rule_quote_id) = 0');

        return $this;
    }

    /**
     * @return $this
     */
    public function getUniqueQuotes()
    {
        $this->getSelect()
            ->join(
                ['temp' => $this->getMainTable()],
                '`main_table`.`rule_quote_id` = `temp`.`rule_quote_id` AND `temp`.`status` != \''
                . RuleQuote::STATUS_COMPLETE
                . '\' AND `temp`.`rule_quote_id` NOT IN (SELECT MAX(`rule_quote_id`) FROM `'
                . $this->getMainTable() . '` WHERE `status` != \'' . RuleQuote::STATUS_COMPLETE . '\' GROUP BY `quote_id`)',
                []
            );

        return $this;
    }
}