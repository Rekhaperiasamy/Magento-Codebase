<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Upsell
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Upsell\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Product.
 */
class Product extends Template implements BlockInterface
{
    /**
     * related sku of the upsell product.
     */
    const RELATED_SKU_ATTRUBTE = 'related_sku';

    /**
     * is pack attribute code.
     */
    const IS_PACK_ATTRIBUTE = 'is_pack';

    /**
     * pack size attribute code.
     */
    const PACK_SIZE_ATTRUBTE = 'pack_size';

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceModel;

    /**
     * Product constructor.
     *
     * @param Context                                       $context
     * @param \Magento\Framework\Registry                   $coreRegistry
     * @param \Magento\Catalog\Model\ProductFactory         $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product  $resourceModel
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->productFactory = $productFactory;
        $this->resourceModel = $resourceModel;
        parent::__construct($context);
    }

    /**
     * @return $this|null
     */
    public function getUpsellProduct()
    {
        $product = $this->getProduct();
        $upsellProduct = null;
        if ($upsellSku = $product->getData(\Dilmah\Upsell\Block\Product::RELATED_SKU_ATTRUBTE)) {
            $productId = $this->resourceModel->getIdBySku($upsellSku);
            $upsellProduct = $this->productFactory->create()->load($productId);
            if ($upsellProduct->isSalable()) {
                return $upsellProduct;
            }
        }
    }

    /**
     * Retrieve currently opened product object.
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }
}
