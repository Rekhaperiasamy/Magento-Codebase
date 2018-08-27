<?php

/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 13/05/2016
 * Time: 10:42
 */

namespace Magenest\AbandonedCartReminder\Model\Resource\Guest;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {

        $this->_init('Magenest\AbandonedCartReminder\Model\Guest', 'Magenest\AbandonedCartReminder\Model\Resource\Guest');
    }
}