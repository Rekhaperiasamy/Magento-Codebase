<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Seooptimization\Model\ResourceModel\Catalog;

use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;

/**
 * Sitemap resource catalog collection model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Category extends \Magento\Sitemap\Model\ResourceModel\Catalog\Category
{
    
   
    public function getCollection($storeId)
    {
        $categories = [];

        /* @var $store \Magento\Store\Model\Store */
        $store = $this->_storeManager->getStore($storeId);

        if (!$store) {
            return false;
        }

        $connection = $this->getConnection();

        $this->_select = $connection->select()->from(
            $this->getMainTable()
        )->where(
            $this->getIdFieldName() . '=?',
            $store->getRootCategoryId()
        );
        $categoryRow = $connection->fetchRow($this->_select);

        if (!$categoryRow) {
            return false;
        }

        $this->_select = $connection->select()->from(
            ['e' => $this->getMainTable()],
            [$this->getIdFieldName(), 'updated_at']
        )->joinLeft(
            ['url_rewrite' => $this->getTable('url_rewrite')],
            'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1'
            . $connection->quoteInto(' AND url_rewrite.store_id = ?', $store->getId())
            . $connection->quoteInto(' AND url_rewrite.entity_type = ?', CategoryUrlRewriteGenerator::ENTITY_TYPE),
            ['url' => 'request_path']
        )->where(
            'e.path LIKE ?',
            $categoryRow['path'] . '/%'
        );

        $this->_addFilter($storeId, 'is_active', 1);
		$this->_addFilter($storeId, 'excludesitemap', 0);

        $query = $connection->query($this->_select);
        while ($row = $query->fetch()) {
            $category = $this->_prepareCategory($row);
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

   
}
