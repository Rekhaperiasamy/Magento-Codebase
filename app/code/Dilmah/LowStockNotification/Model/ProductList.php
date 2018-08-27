<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_LowStockNotification
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\LowStockNotification\Model;

class ProductList extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Low stock factory
     * @var \Magento\Reports\Model\ResourceModel\Product\Lowstock\CollectionFactory
     */
    protected $lowStockFactory;

    /**
     * Store Manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $resourceIterator;

    /**
     * @var array
     */
    protected $products = [];

    /**
     * LowStockNotificationItemList constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reports\Model\ResourceModel\Product\Lowstock\CollectionFactory $lowStockFactory
     * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Reports\Model\ResourceModel\Product\Lowstock\CollectionFactory $lowStockFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
    ) {
        $this->lowStockFactory = $lowStockFactory;
        $this->storeManager = $storeManager;
        $this->resourceIterator = $resourceIterator;
        parent::__construct($context, $registry);
    }

    /**
     * List of low stock items
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLowStockProducts()
    {
        $collection = $this->lowStockFactory->create()
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner')
            ->joinTable(
                'catalog_product_website',
                'product_id = entity_id',
                ['product_id', 'website_id']
            )->filterByIsQtyProductTypes()->joinInventoryItem(
                ['qty']
            )->useManageStockFilter()->useNotifyStockQtyFilter();

        $this->resourceIterator->walk(
            $collection->getSelect(),
            [[$this, 'callbackProduct']]
        );

        return $this->products;
    }

    /**
     * call back function for the product low stock inventory collection
     *
     * @param array $args
     * @return void
     */
    public function callbackProduct($args)
    {
        $row = $args['row'];
        $item = [];

        $item["name"] = $row['name'];
        $item["sku"] = $row['sku'];
        $item["qty"] = intval($row['qty']);

        $this->products[$row['website_id']][] = $item;
    }
}
