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
class BasePriceImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const VALUE_ALL_GROUPS = 'ALL GROUPS';

    const VALUE_ALL_WEBSITES = 'All Websites';

    const COL_SKU = 'sku';

    const COL_SPECIAL_PRICE_WEBSITE = 'website_code'; //'special_price_website';

    //const COL_SPECIAL_PRICE_CUSTOMER_GROUP = 'special_price_customer_group';

    const COL_BASE_PRICE = 'price';

    const COL_SPECIAL_PRICE = 'special_price';

    const COL_SPECIAL_PRICE_FROM_DATE = 'special_from_date';

    const COL_SPECIAL_PRICE_TO_DATE = 'special_to_date';

    const COL_IS_SALE = 'is_sale';

    const COL_TIER_PRICE_QTY = 'tier_price_qty';

    const COL_TIER_PRICE = 'tier_price';

    const TABLE_DECIMAL_PRICE = 'catalog_product_entity_decimal';

    const TABLE_SPECIAL_DATE = 'catalog_product_entity_datetime';

    const TABLE_INT_FLAG = 'catalog_product_entity_int';

    const DEFAULT_ALL_GROUPS_GROUPED_PRICE_VALUE = '0';

    const ENTITY_TYPE_CODE = 'base_price_import';

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
        self::COL_SPECIAL_PRICE_WEBSITE,
        //self::COL_SPECIAL_PRICE_CUSTOMER_GROUP,
        self::COL_BASE_PRICE,
        self::COL_SPECIAL_PRICE,
        self::COL_SPECIAL_PRICE_FROM_DATE,
        self::COL_SPECIAL_PRICE_TO_DATE
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
     * BasePriceImport constructor.
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
        $this->_validators[self::VALIDATOR_WEBSITE] = $websiteValidator;
        //$this->_validators[self::VALIDATOR_TEAR_PRICE] = $tierPriceValidator;
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
     * @return BasePriceImport\Validator|BasePriceImport\Validator\Website
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
        return 'base_price_import';
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
            $this->saveBasePriceImport();
        }

        return true;
    }

    /**
     * Save basic price import
     *
     * @return $this
     */
    public function saveBasePriceImport()
    {
        $this->saveAndReplaceBasePriceImport();
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
     * Replace advanced pricing
     *
     * @return $this
     */
    public function replaceAdvancedPricing()
    {
        $this->saveAndReplaceAdvancedPrices();
        return $this;
    }

    /**
     * Save and replace base price
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function saveAndReplaceBasePriceImport()
    {
        $behavior = $this->getBehavior();
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
            $this->_cachedSkuToDelete = null;
        }
        $listSku = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $basePrice = [];
            $specialPrice = [];
            $specialDates = [];
            $isSale = [];
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
                if (!empty($rowData[self::COL_SPECIAL_PRICE_WEBSITE])) {
                    if ($rowData[self::COL_BASE_PRICE] != '') {
                        $basePrice[$rowSku][self::COL_BASE_PRICE] = [
                            'store_id' => $this->getStoreId($rowData[self::COL_SPECIAL_PRICE_WEBSITE]),
                            'value' => $rowData[self::COL_BASE_PRICE],
                        ];
                    }
                    if ($rowData[self::COL_SPECIAL_PRICE] != '') {
                        $specialPrice[$rowSku][self::COL_SPECIAL_PRICE] = [
                            'store_id' => $this->getStoreId($rowData[self::COL_SPECIAL_PRICE_WEBSITE]),
                            'value' => $rowData[self::COL_SPECIAL_PRICE],
                        ];
                        $isSale[$rowSku][self::COL_IS_SALE] = [
                            'store_id' => 0,
                            'value' => 1,
                        ];
                    }
                    $fromDate = $rowData[self::COL_SPECIAL_PRICE_FROM_DATE];
                    if ($rowData[self::COL_SPECIAL_PRICE_FROM_DATE] != '') {
                        $specialDates[$rowSku][self::COL_SPECIAL_PRICE_FROM_DATE] = [
                            'store_id' => $this->getStoreId($rowData[self::COL_SPECIAL_PRICE_WEBSITE]),
                            'value' => $fromDate,
                        ];
                    } else {
                        //set current date if COL_SPECIAL_PRICE_FROM_DATE is empty and there is a special price
                        if ($rowData[self::COL_SPECIAL_PRICE] != '') {
                            $fromDate = $this->dateTime->gmtDate('Y-m-d');
                        }
                        $specialDates[$rowSku][self::COL_SPECIAL_PRICE_FROM_DATE] = [
                            'store_id' => $this->getStoreId($rowData[self::COL_SPECIAL_PRICE_WEBSITE]),
                            'value' => $fromDate,
                        ];
                    }
                    if ($rowData[self::COL_SPECIAL_PRICE_TO_DATE]) {
                        $specialDates[$rowSku][self::COL_SPECIAL_PRICE_TO_DATE] = [
                            'store_id' => $this->getStoreId($rowData[self::COL_SPECIAL_PRICE_WEBSITE]),
                            'value' => $rowData[self::COL_SPECIAL_PRICE_TO_DATE],
                        ];
                    } elseif (
                        $rowData[self::COL_SPECIAL_PRICE_TO_DATE] == '' && $rowData[self::COL_SPECIAL_PRICE] != ''
                    ) {
                        $specialDates[$rowSku][self::COL_SPECIAL_PRICE_TO_DATE] = [
                            'store_id' => $this->getStoreId($rowData[self::COL_SPECIAL_PRICE_WEBSITE]),
                            'value' => null,
                        ];
                    }
                }
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listSku) {
                    $this->processCountNewPrices($specialPrice);
                    if ($this->deleteProductSpecialPrices(array_unique($listSku), self::TABLE_DECIMAL_PRICE)) {
                        $this->saveProductPrices($specialPrice, self::TABLE_DECIMAL_PRICE);
                        $this->setUpdatedAt($listSku);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $invalidPrices = $this->getInvalidProducts($basePrice);
                foreach ($invalidPrices as $invalidPrice) {
                    unset($basePrice[$invalidPrice['sku']]);
                }
                $this->processCountExistingPrices($basePrice, self::TABLE_DECIMAL_PRICE)
                    ->processCountNewPrices($basePrice);
                $this->saveProductPrices($basePrice, self::TABLE_DECIMAL_PRICE);
                $this->countItemsCreated = 0;
                $this->countItemsUpdated = 0;

                $this->processCountExistingPrices($specialPrice, self::TABLE_DECIMAL_PRICE)
                    ->processCountNewPrices($specialPrice);
                $this->saveProductPrices($specialPrice, self::TABLE_DECIMAL_PRICE);
                $this->countItemsCreated = 0;
                $this->countItemsUpdated = 0;

                $this->processCountExistingFlags($isSale, self::TABLE_INT_FLAG)
                    ->processCountNewFlags($isSale);
                $this->saveProductFlags($isSale, self::TABLE_INT_FLAG);
                $this->countItemsCreated = 0;
                $this->countItemsUpdated = 0;
                $this->processCountExistingSpecialDates($specialDates, self::TABLE_SPECIAL_DATE)
                    ->processCountNewSpecialDates($specialDates);
                $this->saveSpecialDates($specialDates, self::TABLE_SPECIAL_DATE);

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
     * Save product prices.
     *
     * @param array $priceData
     * @param string $table
     * @return $this
     */
    protected function saveProductPrices(array $priceData, $table)
    {
        if ($priceData) {
            $tableName = $this->_resourceFactory->create()->getTable($table);
            $priceIn = [];
            $entityIds = [];
            foreach ($priceData as $sku => $priceRows) {
                if (isset($this->_oldSkus[$sku])) {
                    $productId = $this->_oldSkus[$sku];
                    foreach ($priceRows as $k => $row) {
                        $row['entity_id'] = $productId;
                        $row['attribute_id'] = $this->getAttributeId($k);
                        $priceIn[] = $row;
                        $entityIds[] = $productId;
                    }
                }
            }
            if ($priceIn) {
                $this->_connection->insertOnDuplicate($tableName, $priceIn, ['value']);
            }
        }
        return $this;
    }

    /**
     * Save product flags.
     *
     * @param array $flagData
     * @param string $table
     * @return $this
     */
    protected function saveProductFlags(array $flagData, $table)
    {
        if ($flagData) {
            $tableName = $this->_resourceFactory->create()->getTable($table);
            $priceIn = [];
            $entityIds = [];
            foreach ($flagData as $sku => $flagRows) {
                if (isset($this->_oldSkus[$sku])) {
                    $productId = $this->_oldSkus[$sku];
                    foreach ($flagRows as $k => $row) {
                        $row['entity_id'] = $productId;
                        $row['attribute_id'] = $this->getAttributeId($k);
                        $priceIn[] = $row;
                        $entityIds[] = $productId;
                    }
                }
            }
            if ($priceIn) {
                $this->_connection->insertOnDuplicate($tableName, $priceIn, ['value']);
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
     */
    protected function saveSpecialDates(array $priceData, $table)
    {
        if ($priceData) {
            $tableName = $this->_resourceFactory->create()->getTable($table);
            $priceIn = [];
            $entityIds = [];
            foreach ($priceData as $sku => $priceRows) {
                if (isset($this->_oldSkus[$sku])) {
                    $productId = $this->_oldSkus[$sku];
                    foreach ($priceRows as $k => $row) {
                        $row['entity_id'] = $productId;
                        $row['attribute_id'] = $this->getAttributeId($k);
                        $priceIn[] = $row;
                        $entityIds[] = $productId;
                    }
                }
            }
            if ($priceIn) {
                $this->_connection->insertOnDuplicate($tableName, $priceIn, ['value']);
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
     * Get store id by code
     *
     * @param string $websiteCode
     * @return array|int|string
     */
    protected function getStoreId($websiteCode)
    {
        $result = $websiteCode == $this->_getValidator(self::VALIDATOR_WEBSITE)->getAllWebsitesValue() ||
        $this->_catalogData->isPriceGlobal() ? 0 : $this->_storeResolver->getStoreCodeToId($websiteCode);
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
                ['sku', 'entity_id']
            )
        );
        return $oldSkus;
    }

    /**
     * select the invalid sku's
     * - products with price_type dynamic
     * - products with product type configurable/giftcard
     *
     * @param $skuPrices array
     * @return array
     * @todo check if products of type virtual/grouped has dynamic values or they are not using the "price" attribute
     */
    protected function getInvalidProducts($skuPrices)
    {
        $select = $this->_connection->select()->union(
            [
                //products with price_type dynamic
                $this->_connection->select()->from(
                    ['e' => $this->_connection->getTableName('catalog_product_entity')],
                    ['e.sku']
                )
                    ->joinInner(
                        ['i' => $this->_connection->getTableName('catalog_product_entity_int')],
                        'e.entity_id = i.entity_id',
                        []
                    )
                    ->where(
                        $this->_connection->quoteInto('e.sku IN (?)', array_keys($skuPrices))
                    )
                    ->where(
                        $this->_connection->quoteInto(
                            'i.attribute_id = (?)',
                            [
                                $this->getAttributeId('price_type')
                            ]
                        )
                    )
                    ->where(
                        $this->_connection->quoteInto('i.value = (?)', 0)
                    ),

                //product types that do not use the "price" attribute (configurable/giftcard)
                $this->_connection->select()->from(
                    ['e' => $this->_connection->getTableName('catalog_product_entity')],
                    ['e.sku']
                )
                    ->where(
                        $this->_connection->quoteInto('e.type_id IN (?)', ['configurable', 'giftcard'])
                    )
                    ->where(
                        $this->_connection->quoteInto('e.sku IN (?)', array_keys($skuPrices))
                    ),
            ]
        );
        $products = $this->_connection->fetchAssoc($select);
        return $products;
    }

    /**
     * Count existing prices
     *
     * @param array $prices
     * @param string $table
     * @return $this
     */
    protected function processCountExistingPrices($prices, $table)
    {
        $existingRecords = $this->_connection->fetchAssoc(
            $this->_connection->select()->from(
                ['d' => $this->_connection->getTableName($table)],
                ['value_id', 'attribute_id', 'store_id', 'entity_id', 'value']
            )
                ->joinInner(
                    ['e' => $this->_connection->getTableName('catalog_product_entity')],
                    'd.entity_id = e.entity_id',
                    ['e.sku']
                )
                ->where(
                    $this->_connection->quoteInto('e.sku IN (?)', array_keys($prices))
                )
                ->where(
                    $this->_connection->quoteInto(
                        'd.attribute_id IN (?)',
                        [
                            $this->getAttributeId(self::COL_SPECIAL_PRICE)
                        ]
                    )
                )
        );
        foreach ($existingRecords as $existingRecord) {
            //foreach ($attributes as $k => $attribute) {
            if (isset($prices[$existingRecord['sku']])) {
                $this->incrementCounterUpdated($prices[$existingRecord['sku']], $existingRecord);
            }
            //}
        }

        return $this;
    }

    /**
     * Count existing flags
     *
     * @param array $flags
     * @param string $table
     * @return $this
     */
    protected function processCountExistingFlags($flags, $table)
    {
        $existingRecords = $this->_connection->fetchAssoc(
            $this->_connection->select()->from(
                ['d' => $this->_connection->getTableName($table)],
                ['value_id', 'attribute_id', 'store_id', 'entity_id', 'value']
            )
                ->joinInner(
                    ['e' => $this->_connection->getTableName('catalog_product_entity')],
                    'd.entity_id = e.entity_id',
                    ['e.sku']
                )
                ->where(
                    $this->_connection->quoteInto('e.sku IN (?)', array_keys($flags))
                )
                ->where(
                    $this->_connection->quoteInto(
                        'd.attribute_id IN (?)',
                        [
                            $this->getAttributeId(self::COL_IS_SALE)
                        ]
                    )
                )
        );
        foreach ($existingRecords as $existingRecord) {
            //foreach ($attributes as $k => $attribute) {
            if (isset($flags[$existingRecord['sku']])) {
                $this->incrementCounterUpdated($flags[$existingRecord['sku']], $existingRecord);
            }
            //}
        }

        return $this;
    }

    /**
     * Increment counter of updated items
     *
     * @param array $prices
     * @param array $existingRecord
     * @return void
     */
    protected function incrementCounterUpdated($prices, $existingRecord)
    {
        foreach ($prices as $k => $attribute) {
            //foreach ($attributes as $attribute) {
            if ($existingRecord['store_id'] == $attribute['store_id']
                && $this->getAttributeId($k) == $existingRecord['attribute_id']
            ) {
                $this->countItemsUpdated++;
            }
            //}
        }
    }

    /**
     * Count existing special price dates
     *
     * @param array $prices
     * @param string $table
     * @return $this
     */
    protected function processCountExistingSpecialDates($prices, $table)
    {
        $existingRecords = $this->_connection->fetchAssoc(
            $this->_connection->select()->from(
                ['d' => $this->_connection->getTableName($table)],
                ['value_id', 'attribute_id', 'store_id', 'entity_id', 'value']
            )
                ->joinInner(
                    ['e' => $this->_connection->getTableName('catalog_product_entity')],
                    'd.entity_id = e.entity_id',
                    ['e.sku']
                )
                ->where(
                    $this->_connection->quoteInto('e.sku IN (?)', array_keys($prices))
                )
                ->where(
                    $this->_connection->quoteInto(
                        'd.attribute_id IN (?)',
                        [
                            $this->getAttributeId(self::COL_SPECIAL_PRICE_FROM_DATE),
                            $this->getAttributeId(self::COL_SPECIAL_PRICE_TO_DATE)
                        ]
                    )
                )
        );
        foreach ($existingRecords as $existingRecord) {
            //foreach ($attributes as $k => $attribute) {
            if (isset($prices[$existingRecord['sku']])) {
                $this->incrementCounterUpdated($prices[$existingRecord['sku']], $existingRecord);
            }
            //}
        }

        return $this;
    }

    /**
     * Count new prices
     *
     * @param array $price
     * @return $this
     */
    protected function processCountNewPrices(array $price)
    {
        foreach ($price as $productPrices) {
            $this->countItemsCreated += count($productPrices);
        }
        $this->countItemsCreated -= $this->countItemsUpdated;

        return $this;
    }

    /**
     * Count new flags
     *
     * @param array $flags
     * @return $this
     */
    protected function processCountNewFlags(array $flags)
    {
        foreach ($flags as $flag) {
            $this->countItemsCreated += count($flag);
        }
        $this->countItemsCreated -= $this->countItemsUpdated;

        return $this;
    }

    /**
     * Count new prices
     *
     * @param array $specialPrice
     * @return $this
     */
    protected function processCountNewSpecialDates(array $specialPrice)
    {
        foreach ($specialPrice as $productPrices) {
            $this->countItemsCreated += count($productPrices);
        }
        $this->countItemsCreated -= $this->countItemsUpdated;

        return $this;
    }
}
