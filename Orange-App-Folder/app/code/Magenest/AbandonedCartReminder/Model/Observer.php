<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 20:56
 */

namespace Magenest\AbandonedCartReminder\Model;

class Observer
{

    protected $_logger;

    protected $_rulesFactory;

    protected $type;


    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magenest\AbandonedCartReminder\Model\Resource\Rule\CollectionFactory $rulesFactory
    ) {
        $this->_logger       = $logger;
        $this->_rulesFactory = $rulesFactory;

    }//end __construct()


    public function getType()
    {
        return $this->type;

    }//end getType()


    public function getIdProduct()
    {
        $rules = $this->getMatchingRule();
        $this->_logger->addDebug('$rules');

    }//end getIdProduct()


    public function getMatchingRule()
    {
        $collection = $this->_rulesFactory->create();

        $collection->getRulesByType($this->type);
        $this->_logger->addDebug($collection);
        return $collection;

    }//end getMatchingRule()
}//end class
