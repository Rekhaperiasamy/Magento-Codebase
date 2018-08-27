<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Block\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Attributes.
 */
class Attributes extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $product = null;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Attributes constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param PriceCurrencyInterface                           $priceCurrency
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->coreRegistry->registry('product');
        }

        return $this->product;
    }

    /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array.
     *
     * @param array $excludeAttr
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAdditionalData(array $excludeAttr = [])
    {
        $data = [];
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if ($attribute->getFrontendInput() == 'multiselect') {
                    $value = explode(', ', $value);
                } elseif (!$product->hasData($attribute->getAttributeCode())) {
                    $value = __('N/A');
                } elseif ((string) $value == '') {
                    $value = __('No');
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }

                $data[$attribute->getAttributeCode()] = [
                    'label' => __($attribute->getStoreLabel()),
                    'value' => $value,
                    'code' => $attribute->getAttributeCode(),
                ];
            }
        }

        return $data;
    }
}
