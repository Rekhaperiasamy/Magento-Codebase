<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Plugin\Condition;

class Product
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Amasty\Rules\Helper\Data
     */
    protected $rulesDataHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfigInterface;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $productModel;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Catalog\Model\Product $productModel,
        \Amasty\Rules\Helper\Data $rulesDataHelper
    )
    {
        $this->_objectManager = $objectManager;
        $this->rulesDataHelper = $rulesDataHelper;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->productModel = $productModel;
    }

    public function afterLoadAttributeOptions(
        \Magento\Rule\Model\Condition\Product\AbstractProduct $subject
    ) {
        $attributes = [];
        $attributes['quote_item_sku'] = __('Custom Options SKU');

        if ($this->scopeConfigInterface->getValue(
            'amrules/general/options_values',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ) {
            $attributes['quote_item_value'] = __('Custom Options Values');
        }

        $subject->setAttributeOption(array_merge($subject->getAttributeOption(), $attributes));
        return $subject;
    }

    public function beforeValidate(
        \Magento\Rule\Model\Condition\Product\AbstractProduct $subject,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        if ($object->getProduct() instanceof \Magento\Catalog\Model\Product) {
            $product = $object->getProduct();
        } else {
            $product = $this->productModel->load($object->getProductId());
        }

        if ($product) {
            if ($this->scopeConfigInterface->getValue(
                'amrules/general/options_values',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ) {
                $options = $product->getTypeInstance(true)->getOrderOptions($product);
                $values = '';
                if (isset($options['options']))
                    foreach ($options['options'] as $option)
                        $values .= '|'.$option['value'];

                $product->setQuoteItemValue($values);
            }

            $product->setQuoteItemSku($object->getSku());

            $object->setProduct($product);
        }
    }

}
