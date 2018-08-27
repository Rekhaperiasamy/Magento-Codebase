<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Cron;

use Colissimo\Label\Model\Label;
use Colissimo\Label\Helper\Data as LabelHelper;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Purge
{

    /**
     * @var ShipmentRepositoryInterface $shipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @var Label $label
     */
    protected $label;

    /**
     * @var LabelHelper $labelHelper
     */
    protected $labelHelper;

    /**
     * @var SearchCriteriaBuilder $searchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @param LabelHelper $labelHelper
     * @param Label $label
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DateTime $dateTime
     */
    public function __construct(
        LabelHelper $labelHelper,
        Label $label,
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DateTime $dateTime
    ) {
        $this->labelHelper = $labelHelper;
        $this->shipmentRepository = $shipmentRepository;
        $this->label = $label;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTime = $dateTime;
    }

    /**
     * Execute
     *
     * @return $this
     */
    public function execute()
    {
        $days = $this->labelHelper->getDeleteLabelAfter();

        if (!$days) {
            return $this;
        }

        $date = $this->dateTime->date(null, $this->dateTime->date() . ' - ' . $days . ' days');

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ShipmentInterface::CREATED_AT, $date, 'lteq')
            ->create();

        $shipments = $this->shipmentRepository->getList($searchCriteria);

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        foreach ($shipments as $shipment) {
            $this->label->deleteLabel($shipment->getEntityId());
        }

        return $this;
    }
}