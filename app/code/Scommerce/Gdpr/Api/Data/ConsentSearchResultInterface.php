<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Api\Data;

/**
 * Interface ConsentSearchResultInterface
 * @package Scommerce\Gdpr\Api\Data
 */
interface ConsentSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get choice list
     *
     * @return ConsentInterface[]
     */
    public function getItems();

    /**
     * Set choice list
     *
     * @param ConsentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
