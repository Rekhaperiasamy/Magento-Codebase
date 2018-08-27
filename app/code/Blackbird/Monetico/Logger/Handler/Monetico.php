<?php

namespace Blackbird\Monetico\Logger\Handler;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

class Monetico extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/monetico.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}