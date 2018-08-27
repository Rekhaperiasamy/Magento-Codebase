<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Model\ResourceModel\Product;

/**
 * Product collection
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{

    const CATEGORIES_FILTER_PRODUCT = 0;
    const CATEGORIES_FILTER_PRODUCT_AND_PARENT = 1;
    const CATEGORIES_FILTER_PARENT = 2;

    /**
     * @return boolean
     */
    public function isEnabledFlat()
    {
        return false;
    }

    public function getConfigurableQuantities($storeId)
    {

        $connection = $this->_resource;
        $tableCpsl = $connection->getTableName("catalog_product_super_link");
        $tableCsi = $connection->getTableName("cataloginventory_stock_item");

        $this->addStoreFilter($storeId)
                ->addAttributeToFilter("status", "1")
                ->addAttributeToFilter("type_id", ["eq" => "configurable"])
                ->addAttributeToFilter("visibility", ["neq" => "1"]);
        $this->getSelect()
                ->joinLeft(["cpsl" => $tableCpsl], "cpsl.parent_id=e.entity_id ")
                ->joinLeft(["stock" => $tableCsi], "stock.product_id=cpsl.product_id", ["qty" => "SUM(stock.qty)"])
                ->group(["cpsl.parent_id"]);

        $configurableQty = [];
        foreach ($this as $config) {
            $configurableQty[$config->getId()] = $config->getQty();
        }
        return $configurableQty;
    }

    public function getGroupedProducts(
        $storeId,
        $notLike,
        $concat,
        $listOfAttributes
    ) {

        $connection = $this->_resource;
        $tableCsi = $connection->getTableName("cataloginventory_stock_item");
        $tableCur = $connection->getTableName("url_rewrite");
        $tableCcp = $connection->getTableName("catalog_category_product");
        $tableCcpi = $connection->getTableName("catalog_category_product_index");
        $tableCpl = $connection->getTableName("catalog_product_link");

        $this->addAttributeToFilter("status", 1);
        $this->addAttributeToFilter("type_id", ["eq" => "grouped"]);
        $this->addAttributeToFilter("visibility", ["neq" => 1]);
        $this->addAttributeToSelect($listOfAttributes, true);
        $this->getSelect()
                ->joinLeft(["cpl" => $tableCpl], "cpl.product_id=e.entity_id AND cpl.link_type_id=3", ["child_ids" => "GROUP_CONCAT( DISTINCT cpl.linked_product_id)"])
                ->joinLeft(["stock" => $tableCsi], "stock.product_id=e.entity_id", ["qty" => "qty", "is_in_stock" => "is_in_stock", "manage_stock" => "manage_stock", "use_config_manage_stock" => "use_config_manage_stock", "backorders" => "backorders", "use_config_backorders" => "use_config_backorders"])
                ->joinLeft(["url" => $tableCur], "url.entity_id=e.entity_id " . $notLike . " AND url.entity_type ='product' AND url.store_id=" . $storeId, ["request_path" => $concat . "(DISTINCT request_path)"])
                ->joinLeft(["categories" => $tableCcp], "categories.product_id=e.entity_id", ['category_id', 'product_id', 'position'])
                ->joinLeft(["categories_index" => $tableCcpi], "categories_index.category_id=categories.category_id AND  categories_index.product_id=categories.product_id AND categories_index.store_id=" . $storeId, ["categories_ids" => "GROUP_CONCAT( DISTINCT categories_index.category_id)"])
                ->group(["cpl.product_id"]);


        $grouped = [];
        foreach ($this as $parent) {
            foreach (explode(",", $parent->getChildIds()) as $childId) {
                $grouped[$childId] = $parent;
            }
        }
        return $grouped;
    }

    /**
     *
     * @return array | null
     */
    protected function getParentProducts(
        $type,
        $storeId,
        $notLike,
        $concat,
        $listOfAttributes
    ) {

        $connection = $this->_resource;
        $tableCsi = $connection->getTableName("cataloginventory_stock_item");
        $tableCcp = $connection->getTableName("catalog_category_product");
        $tableCcpi = $connection->getTableName("catalog_category_product_index");
        $tableCpsl = $connection->getTableName("catalog_product_super_link");
        $tableCur = $connection->getTableName("url_rewrite");
        $tableCurpc = $connection->getTableName("catalog_url_rewrite_product_category");

        $this->addStoreFilter($storeId)
                ->addAttributeToFilter("status", "1")
                ->addAttributeToFilter("type_id", ["eq" => $type])
                ->addAttributeToFilter("visibility", ["neq" => "1"])
                ->addAttributeToSelect($listOfAttributes, true);

        $this->getSelect()
                ->joinLeft(["cpsl" => $tableCpsl], "cpsl.parent_id=e.entity_id ", ["child_ids" => "GROUP_CONCAT( DISTINCT cpsl.product_id)"])
                ->joinLeft(["stock" => $tableCsi], "stock.product_id=e.entity_id", ["qty" => "qty", "is_in_stock" => "is_in_stock", "manage_stock" => "manage_stock", "use_config_manage_stock" => "use_config_manage_stock", "backorders" => "backorders", "use_config_backorders" => "use_config_backorders"])
                ->joinLeft(["url" => $tableCur], "url.entity_id=e.entity_id " . $notLike . " AND url.entity_type ='product'  AND url.store_id=" . $storeId, ["request_path" => $concat . "(DISTINCT request_path)"])
                ->joinLeft(["curpc" => $tableCurpc], "url.url_rewrite_id=curpc.url_rewrite_id ")
                ->joinLeft(["categories" => $tableCcp], "categories.product_id=e.entity_id", ['category_id', 'product_id', 'position'])
                ->joinLeft(["categories_index" => $tableCcpi], "categories_index.category_id=categories.category_id AND  categories_index.product_id=categories.product_id AND categories_index.store_id=" . $storeId, ["categories_ids" => "GROUP_CONCAT( DISTINCT categories_index.category_id)"])
                ->group(["cpsl.parent_id"]);

        $parent = [];
        foreach ($this as $p) {
            foreach (explode(",", $p->getChildIds()) as $childId) {
                $parent[$childId] = $p;
            }
        }
        return $parent;
    }

    public function getBundleProducts(
        $storeId,
        $notLike,
        $concat,
        $listOfAttributes
    ) {
        return $this->getParentProducts("bundle", $storeId, $notLike, $concat, $listOfAttributes);
    }

    public function getConfigurableProducts(
        $storeId,
        $notLike,
        $concat,
        $listOfAttributes
    ) {
        return $this->getParentProducts("configurable", $storeId, $notLike, $concat, $listOfAttributes);
    }

    public function getProductCount(
        $storeId,
        $websiteId,
        $notLike,
        $concat,
        $manageStock,
        $listOfAttributes,
        $categoriesFilterList,
        $condition,
        $params
    ) {
        $this->getMainRequest($storeId, $websiteId, $notLike, $concat, $manageStock, $listOfAttributes, $categoriesFilterList, $condition, $params);
        $this->getSelect()->columns("COUNT(DISTINCT e.entity_id) As total");
        $this->getSelect()->limit(1);
        $this->getSelect()->reset(\Zend_Db_Select::GROUP);
        return $this->getFirstItem()->getTotal();
    }

    public function setLimit(
        $sqlSize,
        $loop
    ) {
        $this->getSelect()->limit($sqlSize, ($sqlSize * $loop));
    }

    public function getMainRequest(
        $storeId,
        $websiteId,
        $notLike,
        $concat,
        $manageStock,
        $listOfAttributes,
        $categoriesFilterList,
        $condition,
        $params
    ) {

        $connection = $this->_resource;
        $tableCpsl = $connection->getTableName("catalog_product_super_link");
        $tableCsi = $connection->getTableName("cataloginventory_stock_item");
        $tableCur = $connection->getTableName("url_rewrite");
        $tableCcp = $connection->getTableName("catalog_category_product");
        $tableCcpi = $connection->getTableName("catalog_category_product_index");
        $tableCurpc = $connection->getTableName("catalog_url_rewrite_product_category");
        $tableCpip = $connection->getTableName("catalog_product_index_price");

        $this->addStoreFilter($storeId);

        // only enabled products
        $this->addFieldToFilter("status", 1);

        if (!in_array("*", explode(",", $params["simplegoogleshopping_type_ids"]))) {
            $this->addAttributeToFilter("type_id", ["in" => explode(",", $params["simplegoogleshopping_type_ids"])]);
        }

        if (!in_array("*", explode(",", $params["simplegoogleshopping_visibility"]))) {
            $this->addAttributeToFilter("visibility", ["in" => explode(",", $params["simplegoogleshopping_visibility"])]);
        }

        if (!in_array("*", explode(",", $params["simplegoogleshopping_attribute_sets"]))) {
            $this->addAttributeToFilter("attribute_set_id", ["in" => explode(",", $params["simplegoogleshopping_attribute_sets"])]);
        }

        $this->addAttributeToSelect($listOfAttributes, true);

        $where = "";
        $a = 0;

        $tempFilter = [];

        if ($manageStock != 1 && $manageStock != 0) {
            throw new \Exception(__("Invalid data"));
        } else {
            $manageStock = htmlspecialchars($manageStock);
        }

        foreach (json_decode($params["simplegoogleshopping_attributes"]) as $attributeFilter) {
            if ($attributeFilter->checked) {
                if ($attributeFilter->condition == "in" || $attributeFilter->condition == "nin") {
                    if ($attributeFilter->code == "qty" || $attributeFilter->code == "is_in_stock") {
                        if (!is_array($attributeFilter->value)) {
                            $array = explode(",", $attributeFilter->value);
                        } else {
                            $array = $attributeFilter;
                        }
                        $attributeFilter->value = "'" . implode("','", $array) . "'";
                    } else {
                        if (!is_array($attributeFilter->value)) {
                            $attributeFilter->value = explode(",", $attributeFilter->value);
                        }
                    }
                }
                if (!isset($attributeFilter->statement)) {
                    $attributeFilter->statement = "";
                }
                switch ($attributeFilter->code) {
                    case "qty":
                        if ($a > 0) {
                            $where .= " " . $attributeFilter->statement . " ";
                        }
                        $where .= " qty " . sprintf($condition[$attributeFilter->condition], $attributeFilter->value);

                        $a++;
                        break;
                    case "is_in_stock":
                        if ($a > 0) {
                            $where .= " " . $attributeFilter->statement . " ";
                        }

                        $where .= "(IF(";
                        // use_config_manage_stock=1 && default_manage_stock=0
                        $where .= "(use_config_manage_stock=1 AND $manageStock=0)";
                        // use_config_manage_stock=0 && manage_stock=0
                        $where .= " OR ";
                        $where .= "(use_config_manage_stock=0 AND manage_stock=0)";
                        // use_config_manage_stock=1 && default_manage_stock=1 && in_stock=1
                        $where .= " OR ";
                        $where .= "(use_config_manage_stock=1 AND $manageStock=1 AND is_in_stock=1 )";
                        // use_config_manage_stock=0 && manage_stock=1 && in_stock=1
                        $where .= " OR ";
                        $where .= "(use_config_manage_stock=0 AND manage_stock=1 AND is_in_stock=1 )";
                        $where .= ",'1','0')" . sprintf($condition[$attributeFilter->condition], $attributeFilter->value) . ")";


                        $a++;
                        break;
                    default:
                        if ($attributeFilter->statement == "AND") {
                            if (count($tempFilter)) {
                                $this->addFieldToFilter($tempFilter);
                            }
                            $tempFilter = [];
                        }


                        if ($attributeFilter->condition == "in") {
                            $finset = true;
                            $findInSet = [];
                            foreach ($attributeFilter->value as $v) {
                                if (!is_numeric($v)) {
                                    $finset = true;
                                }
                            }
                            if ($finset) {
                                foreach ($attributeFilter->value as $v) {
                                    $findInSet[] = [["finset" => $v]];
                                }

                                $tempFilter[] = ["attribute" => $attributeFilter->code, $findInSet];
                            } else {
                                $tempFilter[] = ["attribute" => $attributeFilter->code, $attributeFilter->condition => $attributeFilter->value];
                            }
                        } else {
                            $tempFilter[] = ["attribute" => $attributeFilter->code, $attributeFilter->condition => $attributeFilter->value];
                        }


                        break;
                }
            }
        }
        if (count($tempFilter)) {
            $this->addFieldToFilter($tempFilter);
        }

        $this->getSelect()->joinLeft(["stock" => $tableCsi], "stock.product_id=e.entity_id", ["qty" => "qty", "is_in_stock" => "is_in_stock", "manage_stock" => "manage_stock", "use_config_manage_stock" => "use_config_manage_stock", "backorders" => "backorders", "use_config_backorders" => "use_config_backorders"]);
        $this->getSelect()->joinLeft(["url" => $tableCur], "url.entity_id=e.entity_id " . $notLike . " AND url.entity_type ='product' AND url.store_id=" . $storeId, ["request_path" => $concat . "(DISTINCT request_path)"]);
        $this->getSelect()->joinLeft(["curpc" => $tableCurpc], "url.url_rewrite_id=curpc.url_rewrite_id ");

        if ($categoriesFilterList[0] != "*") {
            $v = 0;
            $filter = null;
            foreach ($categoriesFilterList as $categoriesFilter) {
                if ($v > 0) {
                    $filter.=",";
                }
                $expl = explode("/", $categoriesFilter);
                $filter.=array_pop($expl);
                $v++;
            }

            $in = ($params["simplegoogleshopping_category_filter"]) ? "IN" : "NOT IN";
            $this->getSelect()->joinLeft(["cpsl" => $tableCpsl], "cpsl.product_id=e.entity_id ", ["parent_id" => "parent_id"]);
            switch ($params["simplegoogleshopping_category_type"]) {
                case self::CATEGORIES_FILTER_PRODUCT:
                    $ct = "categories.product_id=e.entity_id";
                    break;
                case self::CATEGORIES_FILTER_PRODUCT_AND_PARENT:
                    $ct = "categories.product_id=e.entity_id OR categories.product_id=cpsl.parent_id";
                    break;
                case self::CATEGORIES_FILTER_PARENT:
                    $ct = "categories.product_id=cpsl.parent_id ";
                    break;
            }

            $filter = "AND categories_index.category_id " . $in . "(" . $filter . ")";
            $this->getSelect()->joinLeft(["categories" => $tableCcp], $ct, []);
            $this->getSelect()->joinInner(["categories_index" => $tableCcpi], "categories_index.category_id=categories.category_id AND  categories_index.product_id=categories.product_id AND categories_index.store_id=" . $storeId . " " . $filter, ["categories_ids" => "GROUP_CONCAT( DISTINCT categories_index.category_id)"]);
        } else {
            $this->getSelect()->joinLeft(["categories" => $tableCcp], "categories.product_id=e.entity_id", []);

            $this->getSelect()->joinLeft(["categories_index" => $tableCcpi], "categories_index.category_id=categories.category_id AND  categories_index.product_id=categories.product_id AND categories_index.store_id=" . $storeId, ["categories_ids" => "GROUP_CONCAT(DISTINCT categories_index.category_id)"]);
        }

        $this->getSelect()->joinLeft(["price_index" => $tableCpip], "price_index.entity_id=e.entity_id AND customer_group_id=0 AND  price_index.website_id=" . $websiteId, ["min_price" => "min_price", "max_price" => "max_price", "final_price" => "final_price"]);

        if (!empty($where)) {
            $this->getSelect()->where($where);
        }

        
        
        $this->getSelect()->group("e.entity_id");
        
        return $this;
    }
}
