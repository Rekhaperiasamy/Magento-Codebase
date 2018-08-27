<?php
namespace Orange\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Customer\Api\Data\GroupInterface;

/**
 * Data provider for "Custom Attribute" field of product page
 */
class ContextVisibility extends AbstractModifier
{
    /**
     * @param LocatorInterface            $locator
     * @param UrlInterface                $urlBuilder
     * @param ArrayManager                $arrayManager
	 * @param CustomerGroup               $customerGroup
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
		CustomerGroup $customerGroup
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
		$this->customerGroup = $customerGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->customiseContextVisibilityField($meta);

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Customise Custom Attribute field
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customiseContextVisibilityField(array $meta)
    {
        $fieldCode = 'context_visibility';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');

        if (!$elementPath) {
            return $meta;
        }

        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Context Visibility'),
                            'sortOrder' => 50,
							'collapsible' => true
                        ],
                    ],
                ],
                'children'  => [
                    $fieldCode => [
                        'arguments' => [
                            'data' => [
                                'config' => [
									'formElement' => 'select',
									'componentType' => 'field',
									'options' => $this->getOptions(),
									'visible' => 1,
									'required' => 1,
									'label' => __('Context Visibility')
								],
                            ],
                        ],
                    ]
                ]
            ]
        );		
        return $meta;
    }

    /**
     * Retrieve custom group collection
     *
     * @return array
     */
    protected function getOptions()
    {
        $groupOptions = $this->customerGroup->toOptionArray();
		array_unshift($groupOptions , array('value' => GroupInterface::CUST_GROUP_ALL,'label' => 'ALL CONTEXT'));	
        array_unshift($groupOptions , array('value' => '','label' => 'Select Context'));	
        return $groupOptions;
    }
}