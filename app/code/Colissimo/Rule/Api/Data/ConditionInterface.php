<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ConditionInterface
 */
interface ConditionInterface extends ExtensibleDataInterface
{

    const AGGREGATOR_TYPE_ALL = 'all';

    const AGGREGATOR_TYPE_ANY = 'any';

    /**
     * Get condition type
     *
     * @return string
     */
    public function getConditionType();

    /**
     * @param string $conditionType
     * @return $this
     */
    public function setConditionType($conditionType);


    /**
     * Return list of conditions
     *
     * @return \Colissimo\Rule\Api\Data\ConditionInterface[]|null
     */
    public function getConditions();

    /**
     * Set conditions
     *
     * @param \Colissimo\Rule\Api\Data\ConditionInterface[]|null $conditions
     * @return $this
     */
    public function setConditions(array $conditions = null);

    /**
     * Return the aggregator type
     *
     * @return string|null
     */
    public function getAggregatorType();

    /**
     * Set the aggregator type
     *
     * @param string $aggregatorType
     * @return $this
     */
    public function setAggregatorType($aggregatorType);

    /**
     * Return the operator of the condition
     *
     * @return string
     */
    public function getOperator();

    /**
     * Set the operator of the condition
     *
     * @param string $operator
     * @return $this
     */
    public function setOperator($operator);

    /**
     * Return the attribute name of the condition
     *
     * @return string|null
     */
    public function getAttributeName();

    /**
     * Set the attribute name of the condition
     *
     * @param string $attributeName
     * @return $this
     */
    public function setAttributeName($attributeName);

    /**
     * Return the value of the condition
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Return the value of the condition
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Colissimo\Rule\Api\Data\ConditionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Colissimo\Rule\Api\Data\ConditionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Colissimo\Rule\Api\Data\ConditionExtensionInterface $extensionAttributes
    );
}
