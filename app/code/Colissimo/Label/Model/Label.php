<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Model;

use Colissimo\Label\Helper\Data as LabelHelper;
use Colissimo\Label\Model\Soap;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Sales\Api\ShipmentRepositoryInterface;

/**
 * Class Label
 */
class Label
{
    /**
     * @var ManagerInterface $eventManager
     */
    protected $eventManager;

    /**
     * @var ProductResource $productResource
     */
    protected $productResource;

    /**
     * @var LabelHelper $labelHelper
     */
    protected $labelHelper;

    /**
     * @var FilterManager $filter
     */
    protected $filter;

    /**
     * @var Soap $soap
     */
    protected $soap;

    /**
     * @var ShipmentRepositoryInterface $shipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @param ManagerInterface $eventManager
     * @param LabelHelper $labelHelper
     * @param FilterManager $filter
     * @param Soap $soap
     * @param ProductResource $productResource
     * @param ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        ManagerInterface $eventManager,
        LabelHelper $labelHelper,
        FilterManager $filter,
        Soap $soap,
        ProductResource $productResource,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->eventManager = $eventManager;
        $this->labelHelper = $labelHelper;
        $this->filter = $filter;
        $this->soap = $soap;
        $this->productResource = $productResource;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Do request to shipment
     *
     * @param Request $request
     * @return DataObject
     */
    public function doShipmentRequest($request)
    {
        $response = new DataObject();

        $this->prepareRequest($request);

        if ($request->getData('error')) {
            return $response->setData(['errors' => $request->getData('error')]);
        }

        $this->eventManager->dispatch(
            'colissimo_label_do_shipment_before', ['request' => $request]
        );

        $this->generateLabel($request);

        if ($request->getData('error')) {
            return $response->setData(['errors' => $request->getData('error')]);
        }

        $response->setData('info', [
            [
                'tracking_number' => $request->getData('tracking_number'),
                'label_content'   => $request->getData('label_content'),
            ]
        ]);

        $this->eventManager->dispatch(
            'colissimo_label_do_shipment_after', ['request' => $request, 'response' => $response]
        );

        return $response;
    }

    /**
     * Prepare Request with specific data
     *
     * @param Request $request
     * @return Request
     */
    public function prepareRequest($request)
    {
        $order   = $request->getOrderShipment()->getOrder();
        $address = $order->getShippingAddress();

        if (!is_array($request->getData('packages'))) {
            $packages = $this->initDefaultPackage($order);
            $request->setData('packages', $packages);
            $request->getOrderShipment()->setPackages($packages);
        }

        if (count($request->getData('packages')) > 1) {
            return $request->setData('error', __('Please select only one package.'));
        }

        $weight = $this->getPackageParam($request->getData('packages'), 'weight');
        if (!$weight) {
            $weight = $request->getPackageWeight();
        }
        if (!$weight) {
            $weight = $order->getWeight();
        }
        $request->setPackageWeight($weight);
        $request->getOrderShipment()->setTotalWeight($weight);

        $request->setShipperAddressPostalCode(
            preg_replace('/\s+/', '', $request->getShipperAddressPostalCode())
        );
        $request->setRecipientAddressPostalCode(
            preg_replace('/\s+/', '', $request->getRecipientAddressPostalCode())
        );

        $request->setShipperAddressCountryCode(
            $this->labelHelper->getCountryCode(
                $request->getShipperAddressCountryCode(),
                $request->getShipperAddressPostalCode()
            )
        );
        $request->setRecipientAddressCountryCode(
            $this->labelHelper->getCountryCode(
                $request->getRecipientAddressCountryCode(),
                $request->getRecipientAddressPostalCode()
            )
        );

        if ($this->labelHelper->getStreetLines() >= 3) {
            $request->setData('recipient_address_street_3', $address->getStreetLine(3));
            $request->setData('recipient_address_street_4', $address->getStreetLine(4));
        }

        if (!$request->getData('commercial_name')) {
            $request->setData('commercial_name', $this->labelHelper->getCommercialName());
        }

        if (!$request->getData('order_number')) {
            $request->setData('order_number', $order->getIncrementId());
        }

        if (!$request->getData('product_code')) {
            $request->setData(
                'product_code',
                $this->labelHelper->getProductCode($address->getData('colissimo_product_code'))
            );
        }

        if (!$request->getData('pickup_location_id')) {
            $request->setData('pickup_location_id', $address->getData('colissimo_pickup_id'));
        }

        if (!$request->getData('insurance_value')) {
            if ($this->getPackageParam($request->getData('packages'), 'container')) {
                $insuranceValue = intval($order->getSubtotal() * 100);
                if ($insuranceValue > 150000) {
                    $insuranceValue = 150000;
                }
                $request->setData('insurance_value', $insuranceValue);
            }
        }

        if ($address->getData('colissimo_network_code') == 'X00') {
            $request->setData(
                'fields',
                [
                    'PUDO_NETWORK_CODE'       => $address->getData('colissimo_network_code'),
                    'PUDO_POINT_NAME'         => $address->getCompany(),
                    'PUDO_POINT_ADDRESS_1'    => $address->getStreetLine(1),
                    'PUDO_POINT_ADDRESS_2'    => $address->getStreetLine(2),
                    'PUDO_POINT_TOWN'         => $address->getCity(),
                    'PUDO_POINT_ZIP_CODE'     => $address->getPostcode(),
                    'PUDO_POINT_COUNTRY_CODE' => $address->getCountryId(),
                    'CUSTOMER_ACCOUNT_NUMBER' => $this->labelHelper->getAccountNumber(),
                ]
            );
        }

        /* Add CN23 */
        $addCn23 = $this->labelHelper->isCn23(
            $request->getRecipientAddressCountryCode(),
            $request->getRecipientAddressPostalCode()
        );
        if ($addCn23) {
            $packageItems = $this->getPackageItems($request->getData('packages'));

            $customsDeclaration = [
                'includeCustomsDeclarations' => true,
                'contents' => [
                    'article'  => [],
                    'category' => ['value' => 3],
                ]
            ];

            foreach ($packageItems as $item) {
                $customsDeclaration['contents']['article'][] = [
                    'description'   => $item['name'],
                    'quantity'      => $item['qty'],
                    'weight'        => $item['weight'],
                    'value'         => $item['price'],
                    'hsCode'        => $this->getHsCode($item['product_id']),
                    'originCountry' => 'FR',
                ];
            }

            $request->setData('customs_declarations', $customsDeclaration);

            $totalShippingAmount = (int)($order->getShippingAmount() * 100);
            $request->setData('total_amount', $totalShippingAmount ?: 100);

            if ($this->labelHelper->isDomTom($request->getRecipientAddressPostalCode())) {
                $request->setRecipientAddressCountryCode(
                    $this->labelHelper->getDomTomCountry($request->getRecipientAddressPostalCode())
                );
            }
        }

        return $request;
    }

    /**
     * Generate Label
     *
     * @param Request $request
     * @return Request
     */
    public function generateLabel($request)
    {
        $data = [
            'outputFormat' => [
                'x'                  => 0,
                'y'                  => 0,
                'outputPrintingType' => $this->labelHelper->getLabelSize(),
                'returnType'         => '',
            ],
            'letter' => [
                'service'   => [
                    'productCode'          => $request->getData('product_code'),
                    'depositDate'          => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' +1 days')),
                    'mailBoxPicking'       => false,
                    'transportationAmount' => 0,
                    'totalAmount'          => $request->getData('total_amount'),
                    'orderNumber'          => $request->getData('order_number'),
                    'commercialName'       => $request->getData('commercial_name'),
                    'returnTypeChoice'     => 2,
                ],
                'parcel' => [
                    'insuranceValue'      => $request->getData('insurance_value') ?: 0,
                    'recommendationLevel' => $request->getData('recommendation_level'),
                    'weight'              => $request->getPackageWeight(),
                    'nonMachinable'       => false,
                    'COD'                 => false,
                    'CODAmount'           => 0,
                    'returnReceipt'       => 0,
                    'instructions'        => '',
                    'pickupLocationId'    => $request->getData('pickup_location_id'),
                    'ftd'                 => 0,
                ],
                'sender' => [
                    'senderParcelRef' => $request->getData('order_number'),
                    'address'         => [
                        'companyName'  => $request->getShipperContactCompanyName(),
                        'firstName'    => $request->getShipperContactPersonFirstName(),
                        'lastName'     => $request->getShipperContactPersonLastName(),
                        'line0'        => $request->getShipperAddressStreet2(),
                        'line1'        => $request->getShipperAddressStreet3(),
                        'line2'        => $request->getShipperAddressStreet1(),
                        'line3'        => $request->getShipperAddressStreet4(),
                        'countryCode'  => $request->getShipperAddressCountryCode(),
                        'city'         => $request->getShipperAddressCity(),
                        'zipCode'      => $request->getShipperAddressPostalCode(),
                        'phoneNumber'  => $request->getShipperContactPhoneNumber(),
                        'mobileNumber' => $request->getShipperContactPhoneNumber(),
                        'doorCode1'    => $request->getShipperAddressDoorCode1(),
                        'doorCode2'    => $request->getShipperAddressDoorCode2(),
                        'intercom'     => $request->getShipperAddressIntercom(),
                        'email'        => $request->getShipperEmail(),
                        'language'     => $request->getShipperAddressCountryCode(),
                    ],
                ],
                'addressee' => [
                    'addresseeParcelRef' => $request->getData('order_number'),
                    'codeBarForReference' => true,
                    'serviceInfo'         => $request->getData('service_info'),
                    'address'             => [
                        'companyName'  => $request->getRecipientContactCompanyName(),
                        'firstName'    => $request->getRecipientContactPersonFirstName(),
                        'lastName'     => $request->getRecipientContactPersonLastName(),
                        'line0'        => $request->getRecipientAddressStreet2(),
                        'line1'        => $request->getRecipientAddressStreet3(),
                        'line2'        => $request->getRecipientAddressStreet1(),
                        'line3'        => $request->getRecipientAddressStreet4(),
                        'countryCode'  => $request->getRecipientAddressCountryCode(),
                        'city'         => $request->getRecipientAddressCity(),
                        'zipCode'      => $request->getRecipientAddressPostalCode(),
                        'phoneNumber'  => $request->getRecipientContactPhoneNumber(),
                        'mobileNumber' => $request->getRecipientContactPhoneNumber(),
                        'doorCode1'    => $request->getRecipientAddressDoorCode1(),
                        'doorCode2'    => $request->getRecipientAddressDoorCode2(),
                        'intercom'     => $request->getRecipientAddressIntercom(),
                        'email'        => $request->getRecipientEmail(),
                        'language'     => $request->getRecipientAddressCountryCode(),
                    ],
                ],
            ],
        ];

        if ($request->getData('customs_declarations')) { // CN23
            $data['letter']['customsDeclarations'] = $request->getData('customs_declarations');
        }

        if ($request->getData('fields')) { // International Pickup X00
            $data['generateLabelRequest']['fields'] = ['field' => []];
            foreach ($request->getData('fields') as $field => $value) {
                $data['fields']['field'][] = [
                    'key'   => $field,
                    'value' => $value,
                ];
            }
        }

        if (!is_dir($this->labelHelper->getLabelPath())) {
            return $request->setData('error', __('%1 is not a directory', $this->labelHelper->getLabelPath()));
        }

        $this->soap->setFilePath(
            $this->labelHelper->getLabelPath() . $request->getData('order_number') . '.pdf'
        );

        $response = $this->soap->execute('generateLabel', $data);

        if ($response['error']) {
            return $request->setData('error', $response['error']);
        }

        if ($response['response']->messages->type !== "SUCCESS") {
            return $request->setData('error', $response['response']->messages->messageContent);
        }

        $request->setData(
            [
                'label_content'   => file_get_contents($response['response']->messages->messageContent),
                'tracking_number' => $response['response']->labelResponse->parcelNumber,
            ]
        );

        return $request;
    }

    /**
     * Delete Label
     *
     * @param int $shipmentId
     * @return bool
     */
    public function deleteLabel($shipmentId)
    {
        $shipment = $this->shipmentRepository->get($shipmentId);

        if (!$shipment->getEntityId()) {
            return false;
        }

        if ($shipment->getShippingLabel()) {
            $shipment->setShippingLabel(null);
            $this->shipmentRepository->save($shipment);
        }

        return true;
    }

    /**
     * Retrieve HS code
     *
     * @param int $productId
     * @return string
     */
    protected function getHsCode($productId)
    {
        $value = $this->productResource->getAttributeRawValue($productId, 'product_hs_code', 0);

        if ($value) {
            $value = preg_replace('/\s+/', '', $value);
        }

        return $value;
    }

    /**
     * Init default package if request package is empty
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    protected function initDefaultPackage($order)
    {
        $items = [];

        $method = $order->getShippingMethod(true);
        $insurance = $this->labelHelper->getInsurance($method->getData('carrier_code'), $method->getData('method'));

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $qty = (int)$item->getQtyOrdered() - (int)$item->getQtyCanceled();

            if ($qty) {
                $items[] = [
                    'qty'           => $qty,
                    'customs_value' => '',
                    'name'          => $item->getName(),
                    'weight'        => $item->getWeight(),
                    'product_id'    => $item->getProductId(),
                    'order_item_id' => $item->getParentItemId(),
                    'price'         => $item->getPrice(),
                ];
            }
        }

        $packages = [
            1 => [
                'params' => [
                    'container'          => $insurance,
                    'weight'             => $order->getWeight(),
                    'customs_value'      => '',
                    'length'             => '',
                    'width'              => '',
                    'height'             => '',
                    'weight_units'       => 'KILOGRAM',
                    'dimension_units'    => 'CENTIMETER',
                    'content_type'       => '',
                    'content_type_other' => '',
                ],
                'items' => $items,
            ],
        ];

        return $packages;
    }

    /**
     * Retrieve request params
     *
     * @param array $packages
     * @param string $param
     * @return string|bool
     */
    protected function getPackageParam($packages, $param)
    {
        if (is_array($packages)) {
            foreach ($packages as $package) {
                if (isset($package['params'][$param])) {
                    return $package['params'][$param];
                }
            }
        }

        return false;
    }

    /**
     * Retrieve request params
     *
     * @param array $packages
     * @return array|bool
     */
    protected function getPackageItems($packages)
    {
        if (is_array($packages)) {
            foreach ($packages as $package) {
                if (isset($package['items'])) {
                    return $package['items'];
                }
            }
        }

        return false;
    }
}