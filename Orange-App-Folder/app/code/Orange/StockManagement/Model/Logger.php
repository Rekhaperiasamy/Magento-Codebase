<?php
namespace Orange\StockManagement\Model;

class Logger
{
    /**
     * Logging instance
     * @var \YourNamespace\YourModule\Logger\Logger
     */
    protected $_logger;
    
    /**
     * Constructor
     * @param \YourNamespace\YourModule\Logger\Logger $logger
     */
    public function __construct(\Orange\StockManagement\Logger\Logger $logger)
    {
        $this->_logger = $logger;
    }
    
    public function stockImport($message)
    {
        $this->_logger->info($message);
    }
}