<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\ResourceModel\Csblock;

/**
 * Class Collection
 * @package Aheadworks\Csblock\Model\ResourceModel\Rule
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\ConfigFactory
     */
    protected $_catalogConfFactory;

    /**
     * @var \Magento\Catalog\Model\Entity\AttributeFactory
     */
    protected $_catalogAttrFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\ConfigFactory $catalogConfFactory,
        \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
        $this->_catalogConfFactory = $catalogConfFactory;
        $this->_catalogAttrFactory = $catalogAttrFactory;
        $this->_dateTime = $dateTime;
    }

    public function _construct()
    {
        $this->_init('Magento\Framework\DataObject', 'Aheadworks\Csblock\Model\ResourceModel\Csblock');
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        return $this;
    }

    /**
     * @param $customerGroups
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroup)
    {
        $this->addFieldToFilter('customer_groups', ['finset' => $customerGroup]);
        return $this;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function addPositionFilter($position)
    {
        $this->addFieldToFilter('position', ['eq' => $position]);
        return $this;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function addPageTypeFilter($page)
    {
        $this->addFieldToFilter('page_type', $page);
        return $this;
    }

    public function addStatusEnabledFilter()
    {
        $this->addFieldToFilter('status', ['eq' => 1]);
        return $this;
    }

    public function addDateFilter($currentDate)
    {
        $this
            ->getSelect()
            ->where(
                "(main_table.date_from IS NULL OR main_table.date_from <= '{$currentDate}')
                AND (main_table.date_to IS NULL OR main_table.date_to >= '{$currentDate}')"
            );
        return $this;
    }

    /**
     * currentTime string = hh,mm,ss
     * @param $currentTime
     * @return $this
     */

    public function addTimeFilter($currentTime)
    {
        $this
            ->getSelect()
            ->where(
                "(main_table.time_from = '00,00,00' OR main_table.time_from <= '{$currentTime}')
                AND (main_table.time_to = '00,00,00' OR main_table.time_to >= '{$currentTime}')"
            );
        return $this;
    }

    public function addPatternFilter($timestamp)
    {
        $date = getdate($timestamp);
        $patterns = ['every day', $date['mday'], strtolower(substr($date['weekday'], 0, 2))];
        if ($date['mday'] % 2 != 0) {
            $patterns[] = 'odd days';
        }
        if ($date['mday'] == 0) {
            $patterns[] = 'even days';
        }
        if ($date['mday'] == date('t', $timestamp)) {
            $patterns[] = 'last day';
        }
        if ($date['mday'] != 31 && $date['mday'] % 10 == 1) {
            $patterns[] = '1,11,21';
        }
        if ($date['mday'] % 10 == 1) {
            $patterns[] = '1,11,21,31';
        }
        if ($date['mday'] % 10 == 0) {
            $patterns[] = '10,20,30';
        }
        if ($date['wday'] != 0 && $date['wday'] != 6) {
            $patterns[] = 'mon-fri';
        }
        if ($date['wday'] == 0 || $date['wday'] == 6) {
            $patterns[] = 'sat-sun';
        }
        if ($date['wday'] > 0) {
            $patterns[] = 'mon-sat';
        }

        $this->addFieldToFilter('pattern', ['in' => $patterns]);
        return $this;
    }
}