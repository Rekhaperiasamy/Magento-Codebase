<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Bundle
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Bundle\Plugin\Helper\Catalog\Product;

/**
 * Class Configuration
 */
class Configuration
{
    /**
     * Core data
     *
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Configuration constructor.
     *
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->escaper = $escaper;
    }

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @param \Magento\Bundle\Helper\Catalog\Product\Configuration $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $selection
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings("unused")
     */
    public function aroundGetBundleOptions(
        \Magento\Bundle\Helper\Catalog\Product\Configuration $subject,
        \Closure $proceed,
        $selection
    ) {
        $options = [];
        $product = $selection->getProduct();
        $isPack = $product->getData(\Dilmah\Catalog\Helper\Data::IS_PACK_PRODUCT_ATTRIBUTE) == 1;

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $selection->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : [];
        if ($bundleOptionsIds) {
            /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $selection->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = unserialize($selectionsQuoteItemOption->getValue());

            if (!empty($bundleSelectionIds) && !$isPack) { // no options will be returned if isPack
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $product);

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = ['label' => '', 'value' => []];

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $subject->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                $option['value'][] = $qty . ' x '
                                    . $this->escaper->escapeHtml($bundleSelection->getName());
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }
}
