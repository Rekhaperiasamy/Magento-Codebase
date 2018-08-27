<?php

namespace FME\Geoipultimatelock\Plugin\Catalog\Block\Product\ProductList;

class ToolbarPlugin
{
    
    public function afterGetTotalNum(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $size)
    {

        $size = $subject->getCollection()->getAllIds();

        return count($size);
    }
}
