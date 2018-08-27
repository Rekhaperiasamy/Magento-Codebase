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

use Magento\Catalog\Api\ProductRepositoryInterface;
/**
 * Class GenerateOrders
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GenerateOrders extends Base
{
    /**
     * column name of the sales order table which says order sent to the erp
     */
    const SENT_TO_ERP_ORDER_TABLE_FLAG = 'sent_to_erp';

    /**
     * order status used to filter the order collection
     */
    const ORDER_STATUS_TO_FILTER_ORDERS = 'paid';

    /**
     * Store factory
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $salesOrderCollectionFactory;

    /**
     * Message helper
     *
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $messageHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Locale model
     *
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $localeLists;

    /**
     * Updated order ids of a single cron run
     *
     * @var array
     */
    protected $orderIds = [];

    /**
     * Product Repository
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $_productRepository;

    /**
     * GenerateOrders constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Dilmah\Erp\Logger\Logger $logger
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\Sftp $sftpAdapter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Dilmah\Erp\Logger\Logger $logger,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->storeFactory = $storeFactory;
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->messageHelper = $messageHelper;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->dateTime = $dateTime;
        $this->localeLists = $localeLists;
        $this->_productRepository = $productRepository;
		$this->_logger = $logger;
        parent::__construct($filesystem, $sftpAdapter, $scopeConfig, $resource, $logger);
    }

    /**
     * Prepare order data and generate files and push to ftp for the usage of ERP
     *
     * @return void
     */
    public function execute()
    {
        $this->_logger->debug('debug1234');
		$orderCollection = $this->getOrderCollection();
        $orderData = [];
		$erpData = [];

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orderCollection as $order) {
            $orderDatails = [];
			$erpDatails = [];
			
            $baseData = [
                'increment_id' => $order->getIncrementId(),
                'shipping_address' => $this->generateAddress($order->getShippingAddress()),
                'billing_address' => $this->generateAddress($order->getBillingAddress()),
            ];
			
			$baseerpData = [
                'increment_id' => $order->getIncrementId(),
                'shipping_address' => $this->generateerpAddress($order->getShippingAddress()),
                'billing_address' => $this->generateerpAddress($order->getBillingAddress()),
            ];

            /** @var \Magento\Sales\Model\Order\Item $item */
            foreach ($this->getItemsCollection($order) as $item) {
                $productOptions = $item->getProductOptionByCode('info_buyRequest');
                if ($item->getHasChildren()) {
                    if ($item->getProductType()=='bundle') {
                        $optionsArray = [];
                        $product = $this->_productRepository->getById($item->getProductId());
                        if ($product->getIsEngravable()==1 && !empty($product->getOptions())) {
                            $engravingOptions = $product->getOptions();
                            foreach ($productOptions['options'] as $optionTitleId => $optionValue) {
                                foreach ($engravingOptions as $engravingOption) {
                                    if ($engravingOption->getOptionId() == $optionTitleId) {
                                        $optionTitle = strtolower(trim($engravingOption->getTitle()));
                                        $optionsArray[$optionTitle] = $optionValue;
                                        $engraveTypes = $engravingOption->getValues();
                                        if (in_array($optionTitle, ['engraving style', 'engraving type']) &&
                                            !empty($optionValue) &&
                                            array_key_exists($optionValue, $engraveTypes)
                                        ) {
                                            $optionsArray[$optionTitle] = $engraveTypes[$optionValue]->getTitle();
                                        }
                                    }
                                }
                            }
                        }

                    }
                    continue;
                }
                $itemData = [
                    'sku' => $item->getSku(),
                    'qty' => $item->getQtyInvoiced(),
                ];

                $engravingData['engraving_style'] = null;
                $engravingData['engraving_message'] = null;
                if (!empty($productOptions)) {
                    $options = is_string($productOptions) ? unserialize($productOptions) : $productOptions;
                    if (isset($options['options']) && isset($optionsArray)) {
                        foreach ($optionsArray as $optionLabel => $optionValue) {
                            if (in_array($optionLabel, ['engraving style', 'engraving type'])) {
                                $engravingData['engraving_style'] = $optionValue;
                            }
                            if (in_array($optionLabel, ['engraving message', 'engraving messege'])) {
                                $engravingData['engraving_message'] = $optionValue;
                            }
                        }
                    }

                }


                // if the $items has a parent (item is a child of a bundle or a configurable parent)
                // use the parent item to get the rest of data
                if ($item->getParentItem()) {
                    $item = $item->getParentItem();
                }

                $giftData['gift_wrapping_title'] = $item->getDesign();
                $giftData['gift_message_recipient'] = $item->getRecipient();
                $giftData['gift_message_sender'] = $item->getSender();
                $giftData['gift_message'] = $item->getMessage();

                $orderDatails[] = array_merge($baseData, $itemData, $giftData, $engravingData, ['create' => 'create']);
				$erpDatails[] = array_merge($baseerpData, $itemData, $giftData, $engravingData, ['create' => 'create']);
				
            }

            if (count($orderDatails) > 0) {
                $this->orderIds[] = $order->getEntityId();
                $orderData[$order->getIncrementId()] = $orderDatails;
            }
			
			if (count($erpDatails) > 0) {
                $this->orderIds[] = $order->getEntityId();
                $erpData[$order->getIncrementId()] = $erpDatails;
            }
        }

        // if there are orders with data
        if (count($orderData) > 0) {
            $result = $this->generateFiles($orderData,$erpData); // generate and upload the order files
            if ($result) {
                $this->updateOrders();
            }
        }
    }

    /**
     * Returns the store id array only for Main Website
     *
     * @return array
     */
    public function getMainStoreIds()
    {
        $stores = [];
        $storeCollection = $this->storeFactory->create()->getCollection()
            ->addWebsiteFilter(self::GLOBAL_WEBSITE_ID);

        /** @var \Magento\Store\Model\Store $store */
        foreach ($storeCollection as $store) {
            if (!$store->isActive()) {
                continue;
            }
            $stores[] = $store->getId();
        }

        return $stores;
    }

    /**
     * Return order collection filtered with a pre-defined criteria
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected function getOrderCollection()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $salesOrderCollection */
        $salesOrderCollection = $this->salesOrderCollectionFactory->create();
        $storeIds = $this->getMainStoreIds(); // store ids of Main Website

        $filters = [
            'store_id' => $storeIds,
            'status' => self::ORDER_STATUS_TO_FILTER_ORDERS,
            self::SENT_TO_ERP_ORDER_TABLE_FLAG => 0
        ];
        foreach ($filters as $field => $condition) {
            $salesOrderCollection->addFieldToFilter($field, $condition);
        }

        return $salesOrderCollection->load();
    }

    /**
     * generate pipe(|) separated address
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @return string
     */
    protected function generateerpAddress($address)
    {
        $addressLine = '';
        if ($address) {
            $addressData = [
                'name' => implode(' ', [$address->getFirstname(), $address->getLastname()]),
                'street' => implode(' ', $address->getStreet()),
                'city' => $address->getCity(),
                'state' => $address->getRegion(),
                'postcode' => $address->getPostcode(),
                'country' => (string)$this->localeLists->getCountryTranslation($address->getCountryId())
            ];
			
            $last  =  array_slice($addressData,4);
			$first =  join(' | ', array_slice($addressData, 0, 1));
			$second = join(', ', array_slice($addressData, 1, 3));
			$both  = array_filter(array_merge(array($first), array($second), $last), 'strlen');
			$addressLine = join(' | ', $both);
        }

        return $addressLine;
    }
	
	protected function generateAddress($address)
    {
        $addressLine = '';
        if ($address) {
            $addressData = [
                'name' => implode(' ', [$address->getFirstname(), $address->getLastname()]),
                'street' => implode(' ', $address->getStreet()),
                'city' => $address->getCity(),
                'state' => $address->getRegion(),
                'postcode' => $address->getPostcode(),
                'country' => (string)$this->localeLists->getCountryTranslation($address->getCountryId())
            ];

            $addressLine = '"' . implode('|', $addressData) . '"';
        }

        return $addressLine;
    }

    /**
     * Returns the items collection of the order joined with gift message and gift wrapping
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    protected function getItemsCollection($order)
    {
        $collection = $this->orderItemCollectionFactory->create()->setOrderFilter($order);
        $collection->getSelect()
            ->joinLeft(
                ['gm' => 'gift_message'],
                'main_table.gift_message_id = gm.gift_message_id',
                ['sender', 'recipient', 'message']
            )->joinLeft(
                ['gw' => 'magento_giftwrapping_store_attributes'],
                'main_table.gw_id = gw.wrapping_id',
                ['design']
            );

        if ($order->getId()) {
            foreach ($collection as $item) {
                $item->setOrder($order);
            }
        }
        return $collection;
    }

    /**
     * Generate text files per order and write to the file system and SFTP
     *
     * @param array $orderData
     * @return bool
     */
    protected function generateFiles($orderData,$erpData)
    {
        $overallResult = true;
        foreach ($orderData as $orderId => $order) {
            $content = $this->prepareData($order); // prepare data for the file

            $this->fileInfo['file_name'] = $orderId . '_' . $this->dateTime->timestamp();
            $this->fileInfo['file_format'] = 'txt';
            $this->fileInfo['file_path'] = parent::FTP_ORDER_OUTWARD_DIR;
            $this->fileInfo['server_type'] = parent::SFTP_STORAGE;

            $result = $this->writeData($content);

            if ($result) {
                $this->logger->info(
                    __(
                        'Order file was generated and uploaded for order id: "%1". file name: "%2"',
                        $orderId,
                        $this->fileInfo['file_path'] . '/' .
                        $this->fileInfo['file_name'] . '.' .
                        $this->fileInfo['file_format']
                    )
                );
            } else {
                $overallResult = $result;
                $this->logger->error(__('Order file was NOT generated or uploaded for order id: "%1"', $orderId));
            }
        }
		 foreach ($erpData as $orderId => $order) {
            $content = $this->prepareMdata($order); // prepare data for the file
            $content = str_replace('"', '', $content); 
			
            $this->fileInfo['file_name'] = $orderId . '_' . $this->dateTime->timestamp();
            $this->fileInfo['file_format'] = 'txt';
            $this->fileInfo['file_path'] = parent::FTP_ORDER_MJFERP_DIR;
            $this->fileInfo['server_type'] = parent::SFTP_STORAGE;

            $result = $this->writeData($content);

            if ($result) {
                $this->logger->info(
                    __(
                        'Order file was generated and uploaded for order id: "%1". file name: "%2"',
                        $orderId,
                        $this->fileInfo['file_path'] . '/' .
                        $this->fileInfo['file_name'] . '.' .
                        $this->fileInfo['file_format']
                    )
                );
            } else {
                $overallResult = $result;
                $this->logger->error(__('Order TEST file was NOT generated or uploaded for order id: "%1"', $orderId));
            }
        }
        return $overallResult;
    }

    /**
     * Prepare the array of order data for the file
     *
     * @param array $orderData
     * @return string
     */
    protected function prepareData($orderData)
    {
        $lines = [];
        foreach ($orderData as $item) {
            $lines[] = implode(',', $item);
        }

        return implode(PHP_EOL, $lines);
    }
	 protected function prepareMdata($orderData)
    {
        $lines = [];
        foreach ($orderData as $item) {
            $lines[] = implode('|', $item);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Update the sent to erp flag for the orders
     *
     * @return void
     */
    protected function updateOrders()
    {
        $connection = $this->resource->getConnection('core_write');
        $bind = [self::SENT_TO_ERP_ORDER_TABLE_FLAG => 1];
        $where = ['entity_id IN(?)' => $this->orderIds];
        $connection->update($connection->getTableName('sales_order'), $bind, $where);
    }
}
