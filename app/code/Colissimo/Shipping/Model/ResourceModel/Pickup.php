<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Pickup extends AbstractDb
{

    /**
     * Prefix for resources that will be used in this resource model
     *
     * @var string
     */
    protected $connectionName = 'checkout';

    /**
     * Model initialization
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init('quote_colissimo_pickup', 'quote_id');
    }

    /**
     * Save pickup data for quote
     *
     * @param string $cartId
     * @param string $pickupId
     * @param string $networkCode
     * @return bool
     */
    public function savePickup($cartId, $pickupId, $networkCode)
    {
        $connection = $this->getConnection();

        $data = [
            'quote_id'     => $this->getQuoteId($cartId),
            'pickup_id'    => $pickupId,
            'network_code' => $networkCode
        ];

        $connection->insertOnDuplicate(
            $this->getMainTable(),
            $data,
            array_keys($data)
        );

        return true;
    }

    /**
     * Retrieve current pickup for quote
     *
     * @param string|int $cartId
     * @return array
     */
    public function currentPickup($cartId)
    {
        $connection = $this->getConnection();

        $pickup = $connection->fetchRow(
            $connection->select()
                ->from($this->getMainTable(), ['pickup_id', 'network_code'])
                ->where('quote_id', $this->getQuoteId($cartId))
                ->limit(1)
        );

        return $pickup;
    }

    /**
     * Reset pickup data for quote
     *
     * @param string $cartId
     * @return bool
     */
    public function resetPickup($cartId)
    {
        $connection = $this->getConnection();

        $connection->delete(
            $this->getMainTable(),
            [
                'quote_id = ?' => $this->getQuoteId($cartId)
            ]
        );

        return true;
    }

    /**
     * Retrieve Quote Id
     *
     * @param int|string $cartId
     * @return int
     */
    public function getQuoteId($cartId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getTable('quote_id_mask'), ['quote_id'])
            ->where('masked_id', $cartId)
            ->limit(1);

        $quoteId = $connection->fetchOne($select);

        return $quoteId ?: $cartId;
    }
}