<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Plugin\Block\Product\ProductList;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Toolbar.
 */
class Toolbar
{
    const TOP_RATED_LISTPAGE_SORTER = 'top_rated';
    const IS_NEW_LISTPAGE_SORTER = 'is_new';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Collection
     */
    protected $catalogCollection;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $attributeRepository;


    /**
     * Toolbar constructor.
     * @param StoreManagerInterface $storeManager
     * @param Collection $catalogCollection
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Collection $catalogCollection,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
    ) {
        $this->storeManager = $storeManager;
        $this->catalogCollection = $catalogCollection;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Set collection to pager.
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Magento\Framework\Data\Collection $result
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function afterSetCollection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $result)
    {
        if ($result->getCurrentOrder() == self::TOP_RATED_LISTPAGE_SORTER) {
            $subject->getCollection()->getSelect()->joinLeft(
                ['toprated' => 'review_entity_summary'],
                'toprated.entity_pk_value = e.entity_id AND toprated.entity_type = 1 AND toprated.store_id = '
                . $this->storeManager->getStore()->getId(),
                []
            );
            $subject->getCollection()->getSelect()->order(['toprated.rating_summary DESC']);
        }
        if ($this->catalogCollection->isEnabledFlat()) {
            if ($result->getCurrentOrder() == self::IS_NEW_LISTPAGE_SORTER) {
                $subject->getCollection()->getSelect()->order(['e.is_new DESC', 'e.entity_id DESC']);
            }
        } else {
            $fromPart = $subject->getCollection()->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
            if (!isset($fromPart['flat_isnew'])) {
                $subject->getCollection()->getSelect()->joinLeft(
                    ['flat_isnew' => 'catalog_product_entity_int'],
                    'flat_isnew.row_id = e.entity_id AND flat_isnew.attribute_id = ' . $this->getAttributeId(
                        self::IS_NEW_LISTPAGE_SORTER
                    ),
                    ['flat_isnew.value as isnew_val']
                )
                    ->order('isnew_val DESC');
            }
        }

        return $subject;
    }

    /**
     * get attribute id by attribute code
     * @param string $attributeCode
     * @return mixed
     */
    protected function getAttributeId($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode)->getId();
    }
}
