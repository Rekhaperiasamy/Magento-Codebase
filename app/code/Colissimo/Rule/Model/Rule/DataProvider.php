<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Rule;

use Colissimo\Rule\Model\ResourceModel\Rule\Collection;
use Colissimo\Rule\Model\ResourceModel\Rule\CollectionFactory;
use Colissimo\Rule\Model\Rule;
use Colissimo\Rule\Model\Rule\Metadata\ValueProvider;
use Colissimo\Rule\Model\RegistryConstants;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Registry;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var ValueProvider
     */
    protected $metadataValueProvider;

    /**
     * Initialize dependencies.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param ValueProvider $metadataValueProvider
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Registry $registry,
        ValueProvider $metadataValueProvider,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->coreRegistry = $registry;
        $this->metadataValueProvider = $metadataValueProvider;
        $meta = array_replace_recursive($this->getMetadataValues(), $meta);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get metadata values
     *
     * @return array
     */
    protected function getMetadataValues()
    {
        return $this->metadataValueProvider->getMetadataValues();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var array $items */
        $items = $this->collection->getItems();
        /** @var \Colissimo\Rule\Model\Rule $rule */
        foreach ($items as $rule) {
            $this->loadedData[$rule->getId()] = $rule->getData();
        }

        /** @var \Colissimo\Rule\Model\Rule|null $model */
        $model = $this->coreRegistry->registry(RegistryConstants::CURRENT_COLISSIMO_RULE);
        if (!$model instanceof Rule) {
            return $this->loadedData;
        }

        /** @var array $data */
        $data = $model->getData();
        if (!empty($data)) {
            /** @var \Colissimo\Rule\Model\Rule $rule */
            $rule = $this->collection->getNewEmptyItem();
            $rule->setData($data);
            $this->loadedData[$rule->getId()] = $rule->getData();
            $this->coreRegistry->unregister(RegistryConstants::CURRENT_COLISSIMO_RULE);
        }

        return $this->loadedData;
    }
}
