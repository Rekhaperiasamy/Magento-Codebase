<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */

namespace Amasty\RulesPro\Helper;

class Calculator extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_objectManager = $objectManager;
        $this->resource = $resource;
    }

    public function getThisMonthTotal($customerId)
    {
        $from = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $to = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y')));

        $conditions[] = array ('date'   => ' >= "' . $from . '"');
        $conditions[] = array ('date'   => ' <= "' . $to . '"');
        $conditions[] = array ('status' => ' = "complete"');
        return $this->_getTotals($customerId, $conditions);
    }

    public function getLastMonthTotal($customerId)
    {
        $y = date('Y');
        $m = date('m');
        if (0 == $m - 1) {
            $y = $y - 1 ;
            $m = 12;
        } else {
            $m = $m - 1;
        }
        $last = mktime(0, 0, 0, $m, 1, $y);

        $from = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m', $last), 1, date('Y', $last)));
        $to = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', $last), date('t', $last), date('Y', $last)));

        $conditions[] = array ('date'   => ' >= "' . $from . '"');
        $conditions[] = array ('date'   => ' <= "' . $to . '"');
        $conditions[] = array ('status' => ' = "complete"');

        return $this->_getTotals($customerId, $conditions);

    }

    public function getAllPeriodTotal($customerId)
    {
        $conditions[] = array ('status' => ' = "complete"');
        return $this->_getTotals($customerId);
    }

    public function getSingleTotalField($customerId, $fieldName, $conditions, $conditionType)
    {
        $result = $this->_getTotals($customerId, $conditions, $conditionType);
        return $result[$fieldName];
    }

    /**
     * Calculates aggregated order values for given customer
     *
     * @param int $customerId
     * @param array $conditions  e.g. array( 0=> array('date'=>'>2013-12-04'),  1=>array('status'=>'>2013-12-04'))
     * @param string $conditionType "all"  or "any"
     */
    protected function _getTotals($customerId, $conditions=array(), $conditionType='all')
    {
        return $this->getTotals($customerId, $conditions, $conditionType);
    }

    public function getTotals($customerId, $conditions, $conditionType)
    {
        $db = $this->resource->getConnection('default');

        $select = $db->select()
            ->from(array('o' => $this->resource->getTableName('sales_order')), array())
            ->where('o.customer_id = ?', $customerId)
        ;

        $map = array(
            'date'   =>'o.created_at',
            'status' =>'o.status',
        );

        foreach ($conditions as $element){
            $value = current($element);
            $field = $map[key($element)];
            $w = $field . ' ' . $value;

            if ($conditionType == 'all'){
                $select->where($w);
            } else {
                $select->orWhere($w);
            }
        }

        $select->from(null, array('count' => new \Zend_Db_Expr('COUNT(*)'), 'amount' => new \Zend_Db_Expr('SUM(o.base_grand_total)')));
        $row = $db->fetchRow($select);

        return array('average_order_value' => $row['count'] ? $row['amount'] / $row['count'] : 0,
                     'total_orders_amount' => $row['amount'],
                     'of_placed_orders'    => $row['count'],
        );
    }

}