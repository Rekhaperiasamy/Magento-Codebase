<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dilmah\CsvImport\Model\Import;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

/**
 * Class AdvancedPricing
 *
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class StockImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const VALUE_ALL_GROUPS = 'ALL GROUPS';

    const VALUE_ALL_WEBSITES = 'All Websites';

    const COL_SKU = 'sku';

    const COL_STOCK_WEBSITE = 'stock_website';

    const COL_STOCK_QTY = 'stock_qty';

    const TABLE_CATALOGINVENTORY_STOCK_STATUS = 'cataloginventory_stock_status';

    const TABLE_CATALOGINVENTORY_STOCK_ITEM = 'cataloginventory_stock_item';

    const ENTITY_TYPE_CODE = 'stock_import';

    const VALIDATOR_MAIN = 'validator';

    const VALIDATOR_WEBSITE = 'validator_website';

    const VALIDATOR_TEAR_PRICE = 'validator_tear_price';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_INVALID_WEBSITE => 'Invalid value in Website column (website does not exists?)',
        ValidatorInterface::ERROR_SKU_IS_EMPTY => 'SKU is empty',
        ValidatorInterface::ERROR_SKU_NOT_FOUND_FOR_DELETE => 'Product with specified SKU not found',
        ValidatorInterface::ERROR_INVALID_TIER_PRICE_QTY => 'Tier Price data price or quantity value is invalid',
        ValidatorInterface::ERROR_INVALID_TIER_PRICE_SITE => 'Tier Price data website is invalid',
        ValidatorInterface::ERROR_INVALID_TIER_PRICE_GROUP => 'Tier Price customer group is invalid',
        ValidatorInterface::ERROR_TIER_DATA_INCOMPLETE => 'Tier Price data is incomplete',
        ValidatorInterface::ERROR_INVALID_ATTRIBUTE_DECIMAL =>
            'Value for \'%s\' attribute contains incorrect value, acceptable values are in decimal format',
    ];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        self::COL_SKU,
        self::COL_STOCK_WEBSITE,
        self::COL_STOCK_QTY
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory
     */
    protected $_resourceFactory;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product\StoreResolver
     */
    protected $_storeResolver;

    /**
     * @var ImportProduct
     */
    protected $_importProduct;

    /**
     * @var array
     */
    protected $_validators = [];

    /**
     * @var array
     */
    protected $_cachedSkuToDelete;

    /**
     * @var array
     */
    protected $_oldSkus;

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [self::COL_SKU];

    /**
     * Catalog product entity
     *
     * @var string
     */
    protected $_catalogProductEntity;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $attributeRepository;

    /**
     * @var array
     */
    protected $existingRecords;

    /**
     * @var array
     */
    protected $excludeProductTypes = ['bundle', 'configurable'];

    /**
     * @var array
     */
    protected $oldProductTypes;

    /**
     * @var array
     */
    protected $newSkus = [];

    /**
     * @var array
     */
    protected $updatingSkus = [];

    /**
     * @var array
     */
    protected $changeWebsiteIdStock = [];

    /**
     * @var array
     */
    protected $changeWebsiteIdStockItem = [];

    /**
     * StockImport constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param ImportProduct\StoreResolver $storeResolver
     * @param ImportProduct $importProduct
     * @param BasePriceImport\Validator $validator
     * @param BasePriceImport\Validator\Website $websiteValidator
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver,
        ImportProduct $importProduct,
        BasePriceImport\Validator $validator,
        BasePriceImport\Validator\Website $websiteValidator,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
    ) {
        $this->dateTime = $dateTime;
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection('write');
        $this->_resourceFactory = $resourceFactory;
        $this->_productModel = $productModel;
        $this->_catalogData = $catalogData;
        $this->_storeResolver = $storeResolver;
        $this->_importProduct = $importProduct;
        $this->_validators[self::VALIDATOR_MAIN] = $validator->init($this);
        $this->_oldSkus = $this->retrieveOldSkus();
        $this->oldProductTypes = $this->retrieveOldProductTypes();
        $this->_validators[self::VALIDATOR_WEBSITE] = $websiteValidator;
        $this->errorAggregator = $errorAggregator;
        $this->_catalogProductEntity = $this->_resourceFactory->create()->getTable('catalog_product_entity');
        $this->attributeRepository = $attributeRepository;

        foreach (array_merge($this->errorMessageTemplates, $this->_messageTemplates) as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }
    }

    /**
     * Validator object getter.
     *
     * @param string $type
     * @return StockImport\Validator|StockImport\Validator\Website
     */
    protected function _getValidator($type)
    {
        return $this->_validators[$type];
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'stock_import';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $sku = false;
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        // BEHAVIOR_DELETE use specific validation logic
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (!isset($rowData[self::COL_SKU])) {
                $this->addRowError(ValidatorInterface::ERROR_SKU_IS_EMPTY, $rowNum);
                return false;
            }
            return true;
        }
        if (!$this->_getValidator(self::VALIDATOR_MAIN)->isValid($rowData)) {
            foreach ($this->_getValidator(self::VALIDATOR_MAIN)->getMessages() as $message) {
                $this->addRowError($message, $rowNum);
            }
        }
        if (isset($rowData[self::COL_SKU])) {
            $sku = $rowData[self::COL_SKU];
        }
        if (false === $sku) {
            $this->addRowError(ValidatorInterface::ERROR_ROW_IS_ORPHAN, $rowNum);
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create Advanced price data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            //$this->deleteAdvancedPricing();
            return false;
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            //$this->replaceAdvancedPricing();
            return false;
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveStockImport();
        }

        return true;
    }

    /**
     * Save basic price import
     *
     * @return $this
     */
    public function saveStockImport()
    {
        $this->saveAndReplaceStockImport();
        return $this;
    }

    /**
     * Deletes Advanced price data from raw data.
     *
     * @return $this
     */
    public function deleteAdvancedPricing()
    {
        $this->_cachedSkuToDelete = null;
        $listSku = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowSku = $rowData[self::COL_SKU];
                    $listSku[] = $rowSku;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listSku) {
            $this->deleteProductSpecialPrices(array_unique($listSku), self::TABLE_TIER_PRICE);
            $this->setUpdatedAt($listSku);
        }
        return $this;
    }

    /**
     * Replace stock
     *
     * @return $this
     */
    public function replaceAdvancedPricing()
    {
        $this->saveAndReplaceStockImport();
        return $this;
    }

    /**
     * Save and replace stock
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceStockImport()
    {
        $behavior = $this->getBehavior();
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
            $this->_cachedSkuToDelete = null;
        }
        $listSku = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $stocks = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_SKU_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $rowSku = $rowData[self::COL_SKU];
                $listSku[] = $rowSku;
                if (!empty($rowData[self::COL_STOCK_WEBSITE])) {
                    $stockStatus = 0;
                    if (isset($rowData[self::COL_STOCK_QTY])) {
                        if (!empty($rowData[self::COL_STOCK_QTY]) && $rowData[self::COL_STOCK_QTY] > 0) {
                            $stockStatus = 1;
                        }
                    }
                    //TODO identify the stock_id behavior and apply it
                    $stocks[$rowSku][self::COL_STOCK_QTY] = [
                        'website_id' => $this->getWebsiteId($rowData[self::COL_STOCK_WEBSITE]),
                        'qty' => $rowData[self::COL_STOCK_QTY],
                        'stock_id' => 1,
                        'stock_status' => $stockStatus
                    ];
                    /*TODO removed website_id from the array since it gave duplicates in the cataloginventory_stock_item
                     table. that feature is to be implemented.*/
                    $stockItem[$rowSku][self::COL_STOCK_QTY] = [
                        'website_id' => 1, //$this->getWebsiteId($rowData[self::COL_STOCK_WEBSITE]),
                        'qty' => $rowData[self::COL_STOCK_QTY],
                        'stock_id' => 1,
                        'is_in_stock' => $stockStatus
                    ];
                }
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listSku) {
                    $this->processCountNewStocks($stocks);
                    if (
                    $this->deleteProductSpecialPrices(
                        array_unique($listSku),
                        self::TABLE_CATALOGINVENTORY_STOCK_STATUS
                    )
                    ) {
                        $this->saveProductStockStatus($stocks, self::TABLE_CATALOGINVENTORY_STOCK_STATUS);
                        $this->setUpdatedAt($listSku);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->processCountExistingStockStatus($stocks, self::TABLE_CATALOGINVENTORY_STOCK_STATUS);
                if (!empty($this->changeWebsiteIdStock)) {
                    $stocks = array_replace($stocks, $this->changeWebsiteIdStock);
                }
                $this->processCountNewStocks($stocks);

                $this->abortIfNewSkusAvailable();

                $this->countItemsCreated = 0;
                $this->countItemsUpdated = 0;

                $this->processCountExistingStockItem($stockItem, self::TABLE_CATALOGINVENTORY_STOCK_ITEM);
                if (!empty($this->changeWebsiteIdStockItem)) {
                    $stockItem = array_replace($stockItem, $this->changeWebsiteIdStockItem);
                }
                $this->processCountNewStocks($stockItem);

                $this->abortIfNewSkusAvailable();

                $this->saveProductStockStatus($stocks, self::TABLE_CATALOGINVENTORY_STOCK_STATUS);
                $this->saveProductStockStatusItem($stockItem, self::TABLE_CATALOGINVENTORY_STOCK_ITEM);

                if ($listSku) {
                    $this->setUpdatedAt($listSku);
                }
            }
        }
        return $this;
    }

    /**
     * @param string $attributeCode
     * @return mixed
     */
    protected function getAttributeId($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode)->getId();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function abortIfNewSkusAvailable()
    {
        if ($this->countItemsCreated > 0) {
            $newSkus = array_diff($this->newSkus, $this->updatingSkus);
            $newSkus = implode(',', $newSkus);
            $this->clearTempVars();
            throw new \Exception(
                __(
                    'New SKU\'s are not allowed. ' . $this->countItemsCreated . ' found (' . $newSkus . '). 
                    <br/>Please check if the website code is correct.'
                )
            );
        }
        $this->clearTempVars();
    }

    /**
     * clear temporary variables
     * @return void
     */
    protected function clearTempVars()
    {
        unset($this->newSkus);
        unset($this->updatingSkus);
        $this->newSkus = [];
        $this->updatingSkus = [];
    }

    /**
     * Save product prices.
     *
     * @param array $priceData
     * @param string $table
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function saveProductStockStatus(array $priceData, $table)
    {
        if ($priceData) {
            $tableName = $this->_resourceFactory->create()->getTable($table);
            $priceIn = [];
            $entityIds = [];
            $fields = ['stock_status', 'qty'];
            foreach ($priceData as $sku => $priceRows) {
                if (isset($this->_oldSkus[$sku])) {
                    $productId = $this->_oldSkus[$sku];
                    $typeId = $this->oldProductTypes[$sku];
                    foreach ($priceRows as $k => $row) {
                        if (in_array($typeId, $this->excludeProductTypes)) {
                            $row['qty'] = 0;
                        }
                        $row['product_id'] = $productId;
                        $priceIn[] = $row;
                        $entityIds[] = $productId;
                    }
                }
            }
            if ($priceIn) {
                $this->_connection->insertOnDuplicate($tableName, $priceIn, $fields);
            }
        }
        return $this;
    }

    /**
     * Save product prices.
     *
     * @param array $priceData
     * @param string $table
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function saveProductStockStatusItem(array $priceData, $table)
    {
        if ($priceData) {
            $tableName = $this->_resourceFactory->create()->getTable($table);
            $priceIn = [];
            $entityIds = [];
            $fields = ['is_in_stock', 'qty'];
            foreach ($priceData as $sku => $priceRows) {
                if (isset($this->_oldSkus[$sku])) {
                    $productId = $this->_oldSkus[$sku];
                    foreach ($priceRows as $k => $row) {
                        $row['is_in_stock'] = 1;
                        if (isset($row['qty']) && $row['qty'] == 0) {
                            $row['is_in_stock'] = 0;
                        }
                        $row['product_id'] = $productId;
                        $priceIn[] = $row;
                        $entityIds[] = $productId;
                    }
                }
            }
            if ($priceIn) {
                $this->_connection->insertOnDuplicate($tableName, $priceIn, $fields);
            }
        }
        return $this;
    }

    /**
     * Deletes tier prices prices.
     *
     * @param array $listSku
     * @param string $tableName
     * @return bool
     */
    protected function deleteProductSpecialPrices(array $listSku, $tableName)
    {
        if ($tableName && $listSku) {
            if (!$this->_cachedSkuToDelete) {
                $this->_cachedSkuToDelete = $this->_connection->fetchCol(
                    $this->_connection->select()
                        ->from($this->_catalogProductEntity, 'entity_id')
                        ->where('sku IN (?)', $listSku)
                );
            }
            if ($this->_cachedSkuToDelete) {
                try {
                    $this->countItemsDeleted += $this->_connection->delete(
                        $tableName,
                        $this->_connection->quoteInto('entity_id IN (?)', $this->_cachedSkuToDelete)
                    );
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            } else {
                $this->addRowError(ValidatorInterface::ERROR_SKU_IS_EMPTY, 0);
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Set updated_at for product
     *
     * @param array $listSku
     * @return $this
     */
    protected function setUpdatedAt(array $listSku)
    {
        $updatedAt = $this->dateTime->gmtDate('Y-m-d H:i:s');
        $this->_connection->update(
            $this->_catalogProductEntity,
            [\Magento\Catalog\Model\Category::KEY_UPDATED_AT => $updatedAt],
            $this->_connection->quoteInto('sku IN (?)', array_unique($listSku))
        );
        return $this;
    }

    /**
     * Get website id by code
     *
     * @param string $websiteCode
     * @return array|int|string
     */
    protected function getWebSiteId($websiteCode)
    {
        $result = $websiteCode == $this->_getValidator(self::VALIDATOR_WEBSITE)->getAllWebsitesValue() ||
        $this->_catalogData->isPriceGlobal() ? 0 : $this->_storeResolver->getWebsiteCodeToId($websiteCode);
        return $result;
    }

    /**
     * Get customer group id
     *
     * @param string $customerGroup
     * @return int
     */
    protected function getCustomerGroupId($customerGroup)
    {
        $customerGroups = $this->_getValidator(self::VALIDATOR_TEAR_PRICE)->getCustomerGroups();
        return $customerGroup == self::VALUE_ALL_GROUPS ? 0 : $customerGroups[$customerGroup];
    }

    /**
     * Retrieve product skus
     *
     * @return array
     */
    protected function retrieveOldSkus()
    {
        $oldSkus = $this->_connection->fetchPairs(
            $this->_connection->select()->from(
                $this->_connection->getTableName('catalog_product_entity'),
                ['sku', 'entity_id', 'type_id']
            )
        );
        return $oldSkus;
    }

    /**
     * Retrieve product types
     *
     * @return array
     */
    protected function retrieveOldProductTypes()
    {
        $oldSkus = $this->_connection->fetchPairs(
            $this->_connection->select()->from(
                $this->_connection->getTableName('catalog_product_entity'),
                ['sku', 'type_id']
            )
        );
        return $oldSkus;
    }

    /**
     * Count existing prices
     *
     * @param array $stocks
     * @param string $table
     * @return $this
     */
    protected function processCountExistingStockStatus($stocks, $table)
    {
        $existingRecords = $this->_connection->fetchAll(
            $this->_connection->select()->from(
                ['d' => $this->_connection->getTableName($table)],
                ['product_id', 'website_id', 'stock_id', 'qty', 'stock_status']
            )
                ->joinInner(
                    ['e' => $this->_connection->getTableName('catalog_product_entity')],
                    'd.product_id = e.entity_id',
                    ['e.sku', 'e.type_id']
                )
                ->where(
                    $this->_connection->quoteInto('e.sku IN (?)', array_keys($stocks))
                )
        );
        foreach ($existingRecords as $existingRecord) {
            if (isset($stocks[$existingRecord['sku']])) {
                //replace the website_id with the one of cataloginventory_stock_status
                $this->changeWebsiteIdStock[$existingRecord['sku']]['stock_qty']['website_id'] =
                    $existingRecord['website_id'];
                $this->changeWebsiteIdStock[$existingRecord['sku']]['stock_qty']['qty'] =
                    $stocks[$existingRecord['sku']]['stock_qty']['qty'];
                $this->changeWebsiteIdStock[$existingRecord['sku']]['stock_qty']['stock_id'] =
                    $stocks[$existingRecord['sku']]['stock_qty']['stock_id'];
                $this->changeWebsiteIdStock[$existingRecord['sku']]['stock_qty']['stock_status'] =
                    $stocks[$existingRecord['sku']]['stock_qty']['stock_status'];

                $this->incrementCounterUpdated($stocks[$existingRecord['sku']], $existingRecord);
            }
        }
        return $this;
    }

    /**
     * Increment counter of updated items
     *
     * @param array $stocks
     * @param array $existingRecord
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function incrementCounterUpdated($stocks, $existingRecord)
    {
        foreach ($stocks as $k => $stock) {
            $this->updatingSkus[] = $existingRecord['sku'];
            $this->countItemsUpdated++;
        }
    }

    /**
     * Count existing special price dates
     *
     * @param array $stocks
     * @param string $table
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function processCountExistingStockItem($stocks, $table)
    {
        $existingRecords = $this->_connection->fetchAll(
            $this->_connection->select()->from(
                ['d' => $this->_connection->getTableName($table)],
                ['product_id', 'qty', 'website_id']
            )
                ->joinInner(
                    ['e' => $this->_connection->getTableName('catalog_product_entity')],
                    'd.product_id = e.entity_id',
                    ['e.sku']
                )
                ->where(
                    $this->_connection->quoteInto('e.sku IN (?)', array_keys($stocks))
                )
        );
        foreach ($existingRecords as $existingRecord) {
            if (isset($stocks[$existingRecord['sku']])) {
                //replace the website_id with the one of cataloginventory_stock_item
                $this->changeWebsiteIdStockItem[$existingRecord['sku']]['stock_qty']['website_id'] =
                    $existingRecord['website_id'];
                $this->changeWebsiteIdStockItem[$existingRecord['sku']]['stock_qty']['qty'] =
                    $stocks[$existingRecord['sku']]['stock_qty']['qty'];
                $this->changeWebsiteIdStockItem[$existingRecord['sku']]['stock_qty']['stock_id'] =
                    $stocks[$existingRecord['sku']]['stock_qty']['stock_id'];
                $this->changeWebsiteIdStockItem[$existingRecord['sku']]['stock_qty']['is_in_stock'] =
                    $stocks[$existingRecord['sku']]['stock_qty']['is_in_stock'];

                $this->incrementCounterUpdated($stocks[$existingRecord['sku']], $existingRecord);
            }
        }

        return $this;
    }

    /**
     * Count new stocks
     *
     * @param array $stocks
     * @return $this
     */
    protected function processCountNewStocks(array $stocks)
    {
        foreach ($stocks as $k => $stock) {
            $this->countItemsCreated += count($stock);
            $this->newSkus[] = $k;
        }
        $this->countItemsCreated -= $this->countItemsUpdated;

        return $this;
    }
}
