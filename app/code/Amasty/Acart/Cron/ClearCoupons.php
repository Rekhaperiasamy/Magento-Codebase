<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Cron;

use Magento\Framework\App\ResourceConnection;

class ClearCoupons
{
    protected $_ruleCollectionFactory;
    protected $_dateTime;
    protected $_date;

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ){
        $this->_ruleCollectionFactory = $ruleCollectionFactory;
        $this->_dateTime = $dateTime;
        $this->_date = $date;
    }

    public function execute()
    {
        $formattedDate = $this->_dateTime->formatDate($this->_date->gmtTimestamp());

        $collection = $this->_ruleCollectionFactory->create();

        $collection->join(
                ['history' => $collection->getTable('amasty_acart_history')],
                'main_table.rule_id = history.sales_rule_id',
                []
            )->addFieldToFilter('to_date', ['lt' => $formattedDate]);

        foreach($collection as $coupon)
        {
            $coupon->delete();
        }
    }
}