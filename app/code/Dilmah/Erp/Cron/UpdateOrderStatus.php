<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Erp
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Erp\Cron;

/**
 * Class UpdateOrderStatus
 */
class UpdateOrderStatus extends Base
{
    /**
     * order status to be updated when the order booked in ERP
     */
    const ORDER_STATUS_BOOKED_ERP = 'awaiting_shipment';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $salesOrderCollectionFactory;

    /**
     * UpdateOrderStatus constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Dilmah\Erp\Logger\Logger $logger
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Dilmah\Erp\Logger\Logger $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
    ) {
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        parent::__construct($filesystem, $sftpAdapter, $scopeConfig, $resource, $logger);
    }

    /**
     * Download order status update file from ftp server and update order status
     *
     * @return void
     */
    public function execute()
    {
        $fileList = $this->getFileList(parent::FTP_ORDER_INWARD_DIR);

        if (count($fileList) > 0) {
            foreach ($fileList as $statusFile) {
                $ids = $this->getOrderIds($statusFile['filename']);
                if (count($ids) > 0) {
                    $connection = $this->resource->getConnection('core_write');
                    $bind = ['status' => self::ORDER_STATUS_BOOKED_ERP];
                    $where = ['entity_id IN(?)' => $ids];
                    $count = $connection->update($connection->getTableName('sales_order'), $bind, $where);

                    if (count($count)) {
                        $this->logger->info(
                            __(
                                'Order statuses updated as "%1" for order ids: %2',
                                self::ORDER_STATUS_BOOKED_ERP,
                                implode(', ', $ids)
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * Read the order status update file
     *
     * @param string $fileName
     * @return string
     */
    protected function readFile($fileName)
    {
        $this->fileInfo['file_name'] = $fileName;
        $this->fileInfo['file_path'] = parent::FTP_ORDER_INWARD_DIR;
        $this->fileInfo['server_type'] = parent::SFTP_STORAGE;

        $result = $this->readData();

        return $result;
    }

    /**
     * extract the order ids from the remote file
     *
     * @param string $fileName
     * @return array
     */
    protected function getOrderIds($fileName)
    {
        $orderIds = [];
        $result = $this->readFile($fileName);
        if ($result !== false) {
            $lines = explode(PHP_EOL, $result);

            if (count($lines) > 0) {
                foreach ($lines as $line) {
                    // avoid empty lines
                    if ($line) {
                        $lineData = explode(',', $line);
                        if (!empty($lineData[2]) && $lineData[2] == 'Accepted') {
                            $orderIds[] = $lineData[0];
                        }
                    }
                }
            }
        }
        return $orderIds;
    }
}
