<?php

namespace Orange\StockManagement\Controller\Adminhtml\Stock;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Bundle\Api\Data\LinkInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductTierPriceInterface;

class Csvupload extends Action {

    protected $product;
    private $productRepository;
    public $context;
    protected $_categoryFactory;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->context = $context;
        $this->productRepository = $productRepository;
        $this->_product = $product;
        $this->_categoryFactory = $categoryFactory;


        return parent::__construct($context);
    }

    public function execute() {
        if ($this->getRequest()->getParams('csv_file')) {
            $filename = $_FILES["csv_file"]["name"];
            $source = $_FILES["csv_file"]["tmp_name"];
            $type = $_FILES["csv_file"]["type"];
            $name = explode(".", $filename);
            $mimes = array(
                'application/vnd.ms-excel',
                'text/csv'
            );

            try {
                if (in_array($_FILES['csv_file']['type'], $mimes)) {
                    $all_rows = array();
                    $header = null;
                    $fh = fopen($_FILES['csv_file']['tmp_name'], 'r+');
                    $successMessage = '';
                    $successErrorMessage = '';

                    while ($row = fgetcsv($fh, 8192)) {
                        if ($header === null) {
                            $header = $row;
                            continue;
                        }

                        $all_rows[] = $row;
                    }
                    fclose($fh);

                    foreach ($all_rows as $rowData) {
                        $sku = $rowData[0];
                        if ($this->_product->getIdBySku($sku)) {
                            $date1 = date('Y-m-d');
                            $date2 = date('Y-m-d', strtotime($rowData[2]));
                            if ($date1 == $date2) {
                                $productCollection = $this->productRepository->get($sku);
                                $productId = $productCollection->getRowId();
                                $stockUpdate = $this->_product->load($productId);
                                $stockUpdate->setQuantityAndStockStatus(['qty' => $rowData[1], 'is_in_stock' => 1]);
                                $stockUpdate->save();
                            } else { //Added else condition for Stock Management Update issue (incident: 39116440)
								$this->createStockSchedule($rowData);
							}
                            $successMessage = 1;
                        } else {
                            $successErrorMessage = 1;
                            $this->messageManager->addError("Requested product sku ($sku) doesn't exist");
                        }
                    }
                    if ($successMessage) {
                        $this->messageManager->addSuccess('Stock Schedules Imported Successfully.');
                    }
                } else {
                    $this->messageManager->addError('Wrong File Type. Please insert CSV format file..');
                }
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $model = $objectManager->create('Orange\StockManagement\Model\Stock');
                $model->updatestock();
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('stockmanagement/stock/index');
                return $resultRedirect;
            } catch (Exception $ex) {
                $this->messageManager->addError('Some thing went wrong..');
            }
        } else {
            $this->messageManager->addError('There was a problem with the upload. Please try again.');
        }
    }

    public function createStockSchedule($stockData) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Orange\StockManagement\Model\Stock');
        $timezoneInterface = $objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (isset($log_mode) && $log_mode == 1) {
            $this->logCreate('/var/log/createcategory.log', $stockData[2]);
        }        
        try {
			//commented for Stock Management Update issue (incident: 39116440)
            /*if (rtrim($stockData[2]) == "") {
                $dateTimeZone = $timezoneInterface->date(date("d-m-Y"))->format('Y-m-d H:i:s');
                $model->setData('valid_from', $dateTimeZone);
            } else {*/
                $dateTimeZone = $timezoneInterface->date($stockData[2])->format('Y-m-d H:i:s');
                $model->setData('valid_from', $dateTimeZone);
            //}

            $model->setData('product_sku', $stockData[0]);
            $model->setData('product_qty', trim($stockData[1]));
            $model->save();
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t submit your request, Please try again.'));
            $objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
    }

    function logCreate($fileName, $data) {
        $writer = new \Zend\Log\Writer\Stream(BP . "$fileName");
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);
    }

    public function removeExistingSchedules() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Orange\StockManagement\Model\Stock')->getCollection();
        foreach ($collection as $item) {
            $id = $item->getId();
            $banner = $objectManager->get('Orange\StockManagement\Model\Stock')->load($id);
            $banner->delete();
        }
    }
}
