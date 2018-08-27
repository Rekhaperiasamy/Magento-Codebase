<?php

namespace Blackbird\Monetico\Model;

use Psr\Log\LoggerInterface;

class Debug implements DebugInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var ConfigProvider
     */
    protected $_config;

    /**
     * @param LoggerInterface $logger
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->_logger = $logger;
        $this->_config = $configProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function addDebugMessage($message, $context = [])
    {
        if ($this->_config->isDebugModeEnabled()) {
            $this->_logger->debug($message, $context);
        }

        return $this;
    }
}
