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
 * Class UpdateInventory
 */
class UpdateInventory extends Base
{
    /**
     * @var array
     */
    protected $inventoryData = [];

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * UpdateInventory constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Dilmah\Erp\Logger\Logger $logger
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Dilmah\Erp\Logger\Logger $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($filesystem, $sftpAdapter, $scopeConfig, $resource, $logger);
    }

    /**
     * Download inventory file from ftp server and update the product inventory
     *
     * @return void
     */
    public function execute()
    {
        $fileList = $this->getFileList(parent::FTP_INVENTORY_DIR);

        if (count($fileList) > 0) {
            foreach ($fileList as $statusFile) {
                $skus = $this->getInventoryData($statusFile['filename']);

                // TODO - if the process is slows down: try direct inserting the (qty and status) data and re-indexing
                $productCollection = $this->getProductCollection($skus);
                /** @var \Magento\Catalog\Model\Product $product */
                foreach ($productCollection as $product) {
                    $qty = $this->inventoryData[$product->getSku()];
                    $product->setStockData(
                        [
                            'qty' => $qty, // qty
                            'is_in_stock' => $qty > 0 ? 1 : 0, // Stock Availability
                        ]
                    )->save();
                    $this->logger->info(
                        __('Product inventory updated for sku: "%1" as: "%2"', $product->getSku(), $qty)
                    );
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
        $this->fileInfo['file_path'] = parent::FTP_INVENTORY_DIR;
        $this->fileInfo['server_type'] = parent::SFTP_STORAGE;

        $result = $this->readData();

        return $result;
    }

    /**
     * extract the product inventory data from the remote file and returns a list of skus
     *
     * @param string $fileName
     * @return array
     */
    protected function getInventoryData($fileName)
    {
        $skus = [];
        $result = $this->readFile($fileName);
        $lines = explode(PHP_EOL, $result);

        if (count($lines) > 0) {
            foreach ($lines as $line) {
                if ($line) { // avoid empty lines
                    $lineData = explode(',', $line);
                    $skus[] = $lineData[1];
                    $this->inventoryData[$lineData[1]] = $lineData[2];
                }
            }
        }
        return $skus;
    }

    /**
     * get the matching product collection to the skus
     *
     * @param array $skus
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProductCollection($skus)
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToFilter('sku', $skus);

        return $collection;
    }
}
