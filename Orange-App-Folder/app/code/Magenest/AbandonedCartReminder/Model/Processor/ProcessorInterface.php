<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/09/2015
 * Time: 15:38
 */
namespace Magenest\AbandonedCartReminder\Model\Processor;

interface ProcessorInterface
{


    public function run();


    public function generateAbandonedCartReminder();


    public function getType();


    public function getMatchingRule();
}//end interface
