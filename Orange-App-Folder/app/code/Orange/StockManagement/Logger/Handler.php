<?php
namespace Orange\StockManagement\Logger;

//use Monolog\Logger;
//use Monolog;
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = \Monolog\Logger::INFO;
    
    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/stockupdate/stockupdate.log';
}