<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface RuleSearchResultsInterface
 */
interface RuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get rules.
     *
     * @return \Colissimo\Rule\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set rules .
     *
     * @param \Colissimo\Rule\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
