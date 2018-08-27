<?php

namespace Netresearch\OPS\Controller\Adminhtml\Admin;

use Magento\Framework\App\Filesystem\DirectoryList;

class DownloadLog extends \Magento\Backend\App\Action
{
    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Netresearch\OPS\Model\File\DownloadFactory
     */
    protected $oPSFileDownloadFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * DownloadLog constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Netresearch\OPS\Model\File\DownloadFactory $oPSFileDownloadFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Model\File\DownloadFactory $oPSFileDownloadFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->oPSHelper = $oPSHelper;
        $this->oPSFileDownloadFactory = $oPSFileDownloadFactory;
        $this->fileFactory = $fileFactory;
    }

    public function execute()
    {
        $downloader = $this->oPSFileDownloadFactory->create();
        $fileToDownload = '';
        try {
            $fileToDownload = $downloader->getFile($this->oPSHelper->getLogPath());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        if ($fileToDownload === '') {
            $this->messageManager->addError(__('Log file could not be retrieved.'));
        } else {
            return $this->fileFactory->create(
                \Netresearch\OPS\Helper\Data::LOG_FILE_NAME,
                ['type' => 'filename', 'value' => basename($fileToDownload)],
                DirectoryList::LOG
            );
        }
        return $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }
}
