<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Seo
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Seo\Block;

/**
 * Class ListJson
 * @package Dilmah\Seo\Block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListJson extends \Magento\GoogleTagManager\Block\ListJson
{
    const NUMBER_OF_BAGS_SUFFIX = '_Bags';
    const WEIGHT_IN_GRAMS_SUFFIX = '_Grams';

    /**
     * ListJson constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\GoogleTagManager\Helper\Data $helper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Helper\Cart $checkoutCart
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerColFactory
     * @param \Magento\GoogleTagManager\Model\Banner\Collector $bannerCollector
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\GoogleTagManager\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Helper\Cart $checkoutCart,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerColFactory,
        \Magento\GoogleTagManager\Model\Banner\Collector $bannerCollector,
        array $data = []
    ) {
        $this->_isScopePrivate = true;
        parent::__construct(
            $context,
            $helper,
            $jsonHelper,
            $registry,
            $checkoutSession,
            $customerSession,
            $checkoutCart,
            $layerResolver,
            $moduleManager,
            $request,
            $bannerColFactory,
            $bannerCollector,
            $data
        );
    }

    /**
     * Get display price of the product.
     * @param \Magento\Catalog\Model|Product $product
     * @return int
     */
    public function getDisplayPrice($product)
    {
        // minimal price does not get calculated for fixed bundle products
        return $product->getMinimalPrice() ? $product->getMinimalPrice() : $product->getData('price');

    }

    /**
     * @param \Magento\Catalog\Model|Product $detailedProduct
     * @return string
     */
    public function getProductVariant($detailedProduct)
    {
        if ($detailedProduct->getData(\Dilmah\Catalog\Helper\Data::IS_PACK_PRODUCT_ATTRIBUTE) == 1) {
            $packSizeAttrib = $detailedProduct->getData(\Dilmah\Catalog\Helper\Data::PACK_SIZE_ATTRIBUTE);
            $bagQuantityAttrib = $detailedProduct->getData(\Dilmah\Catalog\Helper\Data::TEA_BAG_QTY_ATTRIBUTE);
            $grammageAttrib = $detailedProduct->getData(\Dilmah\Catalog\Helper\Data::GRAMMAGE_ATTRIBUTE);

            if (!empty($packSizeAttrib)) {
                $packSize = $detailedProduct->getData(\Dilmah\Catalog\Helper\Data::PACK_SIZE_ATTRIBUTE);
            } else {
                return '';
            }
            if (!empty($bagQuantityAttrib)) {
                return $packSize * $detailedProduct->getData(
                    \Dilmah\Catalog\Helper\Data::TEA_BAG_QTY_ATTRIBUTE
                ) . SELF::NUMBER_OF_BAGS_SUFFIX;
            } else {
                if (!empty($grammageAttrib)) {
                    return $packSize * $detailedProduct->getData(
                        \Dilmah\Catalog\Helper\Data::GRAMMAGE_ATTRIBUTE
                    ) . SELF::WEIGHT_IN_GRAMS_SUFFIX;
                }
            }
        } else {
            return '';
        }
    }
}
