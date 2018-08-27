<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Review
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Review\Block\Product\View;

/**
 * Class Ratings.
 */
class Ratings extends \Magento\Framework\View\Element\Template
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Ratings constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context    $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->coreRegistry = $context->getRegistry();
        $this->storeManager = $context->getStoreManager();
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * get the rating percentages for each rating criteria.
     *
     * @return array
     */
    public function getRatingPercentages()
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();

        $select->from(
            ['main_table' => $this->resource->getTableName('rating')],
            ['rating_id', 'rating_code']
        )->join(
            ['store' => $this->resource->getTableName('rating_store')],
            'main_table.rating_id = store.rating_id',
            []
        )->join(
            ['entity' => $this->resource->getTableName('rating_entity')],
            'main_table.entity_id = entity.entity_id AND entity.entity_code = :entity_code',
            []
        )->join(
            ['ra' => $this->resource->getTableName('rating_option_vote_aggregated')],
            'main_table.rating_id = ra.rating_id AND ra.entity_pk_value = :product_id AND ra.store_id = :store_id',
            ['percent_approved']
        )->where(
            'store.store_id = :store_id'
        )->where(
            'main_table.is_active = 1'
        )->order(
            'main_table.position'
        );
        $bind = [
            'entity_code' => \Magento\Review\Model\Rating::ENTITY_PRODUCT_CODE,
            'product_id' => $this->coreRegistry->registry('current_product')->getId(),
            'store_id' => $this->storeManager->getStore()->getStoreId(),
        ];

        $data = $connection->fetchAll($select, $bind);

        return $data;
    }
}
