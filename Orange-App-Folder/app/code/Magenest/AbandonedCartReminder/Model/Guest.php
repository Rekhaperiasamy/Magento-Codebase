<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 13/05/2016
 * Time: 10:40
 */

namespace Magenest\AbandonedCartReminder\Model;


class Guest extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\AbandonedCartReminder\Model\Resource\Guest $resource,
        \Magenest\AbandonedCartReminder\Model\Resource\Guest\Collection $resourceCollection,

        array $data = []
    )
    {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
}