<?php

namespace Netresearch\OPS\Model\Logger;

use Magento\Framework\Filesystem\DriverInterface;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::INFO;

    /**
     * @var string
     */
    protected $fileName = \Netresearch\OPS\Helper\Data::LOG_FILE_NAME;

    /**
     * Handler constructor.
     * @param DriverInterface $filesystem
     * @param null|string $filePath
     * @param null|string $filename
     */
    public function __construct(DriverInterface $filesystem, $filePath = null, $filename = null)
    {
        if (null !== $filename) {
            $this->fileName = $filename;
        }
        if (null === $filePath) {
            $filePath = BP . sprintf('/%s/', \Netresearch\OPS\Helper\Data::getPathToLogFile());
        }
        parent::__construct($filesystem, $filePath);
    }
}
