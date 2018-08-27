<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Import;

class Import extends \Magento\Backend\App\Action
{

    protected $_geoipultimatelockHelper;
    protected $_resultPageFactory;
    protected $_resource;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \FME\Geoipultimatelock\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context);
        $this->_geoipultimatelockHelper = $helper;
        $this->_resource = $resource;
        $this->_resultPageFactory = $pageFactory;
    }
    
    /**
     * @return void
     */
    public function execute()
    {

        $resource = $this->_resource;
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Geoipultimatelock::main_menu');
        $resultPage->getConfig()
                ->getTitle()
                ->prepend(__('Import Countries List'));

        try {
            // check if file exists
            $csvFilePath = $this->_geoipultimatelockHelper->prepareCsv('GeoIPCountryWhois');

            if (!file_exists($csvFilePath)) {
                $this->messageManager->addError(__('File GeoIPCountryWhois.csv does not exist!'));
                return $resultRedirect->setPath('*/*/index');
            }

            // make csv in parts with each file having 2000 lines
            if ($this->_geoipultimatelockHelper->prepareCsvParts($csvFilePath) === false) {
                $this->messageManager->addError(__('Unknown Error occured!'));
                return $resultRedirect->setPath('*/*/index');
            }

            $number = 0;

            $found = true;

            $partno = 0;

            ini_set('max_execution_time', 9000);

            while ($found) {
                $partCsvPath = $this->_geoipultimatelockHelper->prepareCsv('GeoIPCountryWhois_' . $partno);

                $sql = array();

                if (file_exists($partCsvPath)) {
                    $found = true;

                    $write = $resource->getConnection('core_write');

                    if ($partno == 0) {
                        //each time record is imported, tables will be truncated
                        $write->truncateTable($resource->getTableName('geoip_csv'));
                        $write->truncateTable($resource->getTableName('geoip_cl'));
                        $write->truncateTable($resource->getTableName('geoip_ip'));
                    }

                    if (($handle = fopen($partCsvPath, 'r')) !== false) {
                        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                            $sql[] = '("' . $row[0] . '","' . $row[1] . '","' . $row[2] . '","' . $row[3] . '","' . $row[4] . '","' . $row[5] . '")';
                            $number++;
                            unset($row);
                        }

                        fclose($handle);
                    }

                    try {
                        $write->beginTransaction();

                        $query = "INSERT INTO " . $resource->getTableName('geoip_csv')
                                . " (start_ip, end_ip, start, end, cc, cn) "
                                . " VALUES " . implode(',', $sql);

                        $write->exec($query);
                        $write->commit();
                    } catch (\Exception $e) {
                        $write->rollBack();
                        $this->messageManager->addError($e->getMessage());
                        return $resultRedirect->setPath('*/*/index');
                    }

                    //remove part file after transaction completes
                    unlink($partCsvPath);
                    $partno++;
                } else {
                    $found = false;
                }
            }

            if ($partno != 0) {
                $write = $resource->getConnection('core_write');

                try {
                    $write->beginTransaction();
                    $write->exec(
                        "
                        INSERT INTO " . $resource->getTableName('geoip_cl')
                        . " SELECT DISTINCT NULL, cc, cn FROM " . $resource->getTableName('geoip_csv')
                    );
                    
                    $write->exec(
                        "
                        INSERT INTO " . $resource->getTableName('geoip_ip')
                            . " SELECT start, end, ci FROM " . $resource->getTableName('geoip_csv')
                            . " NATURAL JOIN " . $resource->getTableName('geoip_cl') . ";
                    "
                    );
                    $write->commit();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $write->rollBack();
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/index');
                }
            }

            $this->messageManager->addSuccess(__('Number of records imported: ' . $number));

            return $resultRedirect->setPath('*/*/index');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/index');
        }

        return $resultPage;
    }

    /**
     * News access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {

        return $this->_authorization->isAllowed('FME_Geoipultimatelock::import_start');
    }
}
