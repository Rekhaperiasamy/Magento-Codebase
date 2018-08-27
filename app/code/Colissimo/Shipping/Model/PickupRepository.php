<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model;

use Colissimo\Shipping\Api\PickupRepositoryInterface;
use Colissimo\Shipping\Api\Data\PickupSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Exception;

/**
 * Class PickupRepository
 */
class PickupRepository implements PickupRepositoryInterface
{

    /**
     * @var PickupFactory $pickupFactory
     */
    protected $pickupFactory;

    /**
     * @var PickupSearchResultsInterfaceFactory $searchResultsFactory
     */
    protected $searchResultsFactory;

    /**
     * PickupRepository constructor.
     *
     * @param PickupFactory $pickupFactory
     * @param PickupSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        PickupFactory $pickupFactory,
        PickupSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->pickupFactory = $pickupFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($pickupId, $network)
    {
        $pickup = $this->pickupFactory->create();
        $pickup->load($pickupId, $network);

        if (!$pickup->hasData()) {
            throw new Exception(__('Unable to load pickup, please select another shipping method'));
        }

        return $pickup;
    }

    /**
     * {@inheritdoc}
     */
    public function current($cartId)
    {
        $pickup = $this->pickupFactory->create();
        $pickup->current($cartId);

        return $pickup;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $pickup = $this->pickupFactory->create();

        $required = ['street', 'city', 'postcode', 'country'];

        $data = [];

        foreach ($searchCriteria->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                $data[$filter->getField()] = $filter->getValue();
            }
        }

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new Exception(__('%1 field is required', $field));
            }
        }

        $list = $pickup->getList($data['street'], $data['city'], $data['postcode'], $data['country']);

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($list->getItems());
        $searchResult->setTotalCount($list->getSize());

        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function save($cartId, $pickupId, $networkCode)
    {
        $pickup = $this->pickupFactory->create();

        return $pickup->save($cartId, $pickupId, $networkCode);
    }

    /**
     * {@inheritdoc}
     */
    public function reset($cartId)
    {
        $pickup = $this->pickupFactory->create();

        return $pickup->reset($cartId);
    }
}