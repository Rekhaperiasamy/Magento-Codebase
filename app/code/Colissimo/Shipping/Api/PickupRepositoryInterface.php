<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface PickupInterface
 */
interface PickupRepositoryInterface
{

    /**
     * Get pickup
     *
     * @param string $pickupId
     * @param string $networkCode
     * @return \Colissimo\Shipping\Api\Data\PickupInterface
     */
    public function get($pickupId, $networkCode);

    /**
     * Get pickup list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Colissimo\Shipping\Api\Data\PickupSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve current pickup data for quote
     *
     * @param string $cartId
     * @return \Colissimo\Shipping\Api\Data\PickupInterface
     */
    public function current($cartId);

    /**
     * Save pickup data for quote
     *
     * @param string $cartId
     * @param string $pickupId
     * @param string $networkCode
     * @return bool
     */
    public function save($cartId, $pickupId, $networkCode);

    /**
     * Reset pickup data for quote
     *
     * @param string $cartId
     * @return bool
     */
    public function reset($cartId);
}