<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Controller\Adminhtml\Shipping;

use Colissimo\Label\Helper\Data as LabelHelper;
use Colissimo\Label\Model\Deposit\Pdf;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;

class Deposit extends Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Colissimo_Label::deposit';

    /**
     * @var LabelHelper $labelHelper
     */
    protected $labelHelper;

    /**
     * @var ShipmentRepositoryInterface $shipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @var OrderAddressRepositoryInterface $orderAddressRepository
     */
    protected $orderAddressRepository;

    /**
     * @var Pdf $pdf
     */
    protected $pdf;

    /**
     * @var FileFactory $fileFactory
     */
    protected $fileFactory;

    /**
     * @var Filter $filter
     */
    protected $filter;

    /**
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param LabelHelper $labelHelper
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param OrderAddressRepositoryInterface $orderAddressRepository
     * @param FileFactory $fileFactory
     * @param Pdf $pdf
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        LabelHelper $labelHelper,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderAddressRepositoryInterface $orderAddressRepository,
        FileFactory $fileFactory,
        Pdf $pdf,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->labelHelper            = $labelHelper;
        $this->shipmentRepository     = $shipmentRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->fileFactory            = $fileFactory;
        $this->pdf                    = $pdf;
        $this->filter                 = $filter;
        $this->collectionFactory      = $collectionFactory;
    }

    /**
     * Action
     * @return void
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $data = [
            'customer' => [
                'account_number'  => $this->labelHelper->getAccountNumber(),
                'commercial_name' => $this->labelHelper->getCommercialName(),
            ],
            'site' => [
                'name'   => $this->labelHelper->getSiteName(),
                'number' => $this->labelHelper->getSiteNumber(),
            ],
            'shipments' => [],
        ];

        $totalWeight = 0;
        $totalCount  = 0;

        foreach ($collection as $shipment) {
            if (!$shipment->getEntityId()) {
                continue;
            }

            $shippingAddress = $this->orderAddressRepository->get($shipment->getShippingAddressId());

            foreach ($shipment->getTracks() as $track) {

                if ($track->getCarrierCode() !== 'colissimo') {
                    continue;
                }

                $data['shipments'][] = [
                    'increment_id' => $shipment->getIncrementId(),
                    'name'         => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
                    'tracking'     => $track->getTrackNumber(),
                    'postcode'     => $shippingAddress->getPostcode(),
                    'country'      => $shippingAddress->getCountryId(),
                    'weight'       => floatval($shipment->getTotalWeight()),
                    'nm'           => 0
                ];

                $totalWeight += $shipment->getTotalWeight();
                $totalCount++;
            }
        }

        $data['summary'] = [
            'total_shipment' => $totalCount,
            'total_weight'   => $totalWeight,
        ];

        $this->fileFactory->create($this->pdf->getFileName(), $this->pdf->getFile($data), DirectoryList::TMP);
    }
}