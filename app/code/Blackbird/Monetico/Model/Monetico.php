<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Model;

use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Service\ShipmentService;
use Magento\Sales\Model\Convert\OrderFactory as ConvertOrderFactory;
use Blackbird\Monetico\Helper\DataSealedForm;

class Monetico
{
    /**
     * CODE-RETOUR
     */
    const PAYTEST = 'payetest';
    const PAIEMENT = 'paiement';
    const ANNULATION = 'Annulation';

    /**
     * CVX
     */
    const OUI = 'oui';
    const NON = 'non';

    /**
     * BRAND
     */
    const AMERICAN_EXPRESS = 'AM';
    const GIE_CB = 'CB';
    const MASTERCARD = 'MC';
    const VISA = 'VI';
    const UNAVAILABLE = 'na';

    /**
     * STATUS 3DS
     */
    const NO_SECURE = -1;
    const LOW_RISK = 1;
    const UNABLE_BUT_AUTHENTICATE = 2;
    const HIGH_RISK = 3;
    const CRITIC_RISK = 4;

    /**
     * MOTIF REFUS
     */
    const NEED_INFO = 'Appel Phonie';
    const REFUSED = 'Refus';
    const FORBIDDEN = 'Interdit';
    const BLOCKED_FRAUD_FILTER = 'filtrage';
    const BLOCKED_FRAUD_SCORING = 'scoring';
    const FAIL_AUTHENTICATE = '3DSecure';

    /**
     * @var Order
     */
    protected $_orderModel;

    /**
     * @var OrderSender
     */
    protected $_orderSender;

    /**
     * @var InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var ShipmentService
     */
    protected $_shipmentService;

    /**
     * @var ConvertOrderFactory
     */
    protected $_convertOrder;

    /**
     * @var DataSealedForm
     */
    protected $_dataSealedFormHelper;

    /**
     * @param OrderFactory $orderFactory
     * @param OrderSender $orderSender
     * @param InvoiceService $invoiceService
     * @param ShipmentService $shipmentService
     * @param ConvertOrderFactory $convertOrderFactory
     * @param DataSealedForm $dataSealedForm
     */
    public function __construct(
        OrderFactory $orderFactory,
        OrderSender $orderSender,
        InvoiceService $invoiceService,
        ShipmentService $shipmentService,
        ConvertOrderFactory $convertOrderFactory,
        DataSealedForm $dataSealedForm
    ) {
        $this->_orderModel = $orderFactory->create();
        $this->_orderSender = $orderSender;
        $this->_invoiceService = $invoiceService;
        $this->_shipmentService = $shipmentService;
        $this->_convertOrder = $convertOrderFactory->create();
        $this->_dataSealedFormHelper = $dataSealedForm;
    }

    /**
     * Load an order by increment ID
     *
     * @param string $incrementId
     * @return bool
     */
    public function loadOrder($incrementId)
    {
        $this->_orderModel->loadByIncrementId($incrementId);
        $exists = ($this->_orderModel->getId() && $this->_orderModel->getPayment() && $this->_orderModel->getPayment()->getMethod());

        if ($exists) {
            $this->_dataSealedFormHelper->init($this->_orderModel->getPayment()->getMethod(), $this->_orderModel);
        }

        return $exists;
    }

    /**
     * Retrieve the payment method
     *
     * @return Order
     */
    public function getOrderMethod()
    {
        return $this->_orderModel->getPayment()->getMethod();
    }

    /**
     * Check if the state order is completed
     *
     * @return bool
     */
    public function isOrderComplete()
    {
        return $this->_orderModel->getState() === Order::STATE_COMPLETE;
    }

    /**
     * Check if the state order is processing
     *
     * @return bool
     */
    public function isOrderProcessing()
    {
        return $this->_orderModel->getState() === Order::STATE_PROCESSING;
    }

    /**
     * Unhold an order if possible
     *
     * @return $this
     */
    public function unholdOrder()
    {
        if ($this->_orderModel->canUnhold()) {
            $this->_orderModel->unhold();
        }

        return $this;
    }

    /**
     * Add history to the order
     *
     * @param string $message
     * @param string $status
     * @param bool $sendNewEmail
     * @return $this
     */
    public function updateOrder($message, $status = null, $sendNewEmail = false)
    {
        if (!empty($status)) {
            $this->_orderModel->setState($status);
            $this->_orderModel->addStatusToHistory($status, $message, true);
        } else {
            $this->_orderModel->addStatusHistoryComment($message);
        }

        if ($sendNewEmail === true && $this->_orderModel->getCanSendNewEmailFlag()) {
            $this->_orderSender->send($this->_orderModel);
        }

        $this->_orderModel->save();

        return $this;
    }

    /**
     * Invoice the order if possible
     *
     * @return bool
     */
    public function saveInvoice()
    {
        $status = false;

        if ($this->_orderModel->canInvoice()) {
            $invoice = $this->_invoiceService->prepareInvoice($this->_orderModel)->register();

            if ($invoice->canCapture()) {
                $invoice->capture();
            }

            $invoice->save();
            $status = true;
        }

        return $status;
    }

    /**
     * Save the shipment if possible
     *
     * @return bool
     */
    public function saveShipment()
    {
        $status = false;

        if ($this->_orderModel->canShip()) {
            $shipment = $this->_convertOrder->toShipment($this->_orderModel);

            foreach ($this->_orderModel->getAllItems() as $item) {
                if (!$item->getQtyToShip() || $item->getIsVirtual()) {
                    continue;
                }

                $shipmentItem = $this->_convertOrder->itemToShipmentItem($item)->setQty($item->getQtyToShip());
                $shipment->addItem($shipmentItem);
            }

            $shipment->register();
            $shipment->save();
            $this->_shipmentService->notify($shipment->getId());
            $status = true;
        }

        return $status;
    }

    /**
     * Hold or Cancel the order by status
     *
     * @param $status
     * @return bool
     */
    public function holdOrCancelOrder($status)
    {
        $canceled = false;

        if ($status === Order::STATE_HOLDED && $this->_orderModel->canHold()) {
            $this->_orderModel->hold();
        } elseif ($status === Order::STATE_CANCELED) {
            $this->cancelOrder();
            $canceled = true;
        }

        return $canceled;
    }

    /**
     * Cancel the order if possible
     *
     * @return $this
     */
    public function cancelOrder()
    {
        if ($this->_orderModel->canCancel()) {
            $this->_orderModel->cancel();
        }

        return $this;
    }

    /**
     * Retrieve the monetico order response hmac
     *
     * @param array $data
     * @return string
     */
    public function getOrderResponseMAC(array $data)
    {
        return $this->_dataSealedFormHelper->getResponseMAC($data);
    }

    /**
     * Retrieve the monetico order hmac
     *
     * @return string
     */
    public function getOrderMAC()
    {
        $mac = '';
        $data = $this->_dataSealedFormHelper->getData();

        if (isset($data['MAC'])) {
            $mac = $data['MAC'];
        }

        return $mac;
    }

    /**
     * Retrieve the current order status
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->_orderModel->getStatus();
    }

    /**
     * @return array
     */
    public function getBackCodes()
    {
        return [
            self::PAYTEST,
            self::PAIEMENT,
            self::ANNULATION,
        ];
    }

    /**
     * @return array
     */
    public function getCvx()
    {
        return [
            self::OUI => __('Yes'),
            self::NON => __('No'),
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    public function getCvxTranslate($code)
    {
        $cvxs = $this->getCvx();
        $cvx = '';

        if (isset($cvxs[$code])) {
            $cvx = $cvxs[$code];
        }

        return $cvx;
    }

    /**
     * @return array
     */
    public function getBrands()
    {
        return [
            self::AMERICAN_EXPRESS => __('American Express'),
            self::GIE_CB => __('Credit Card CB'),
            self::MASTERCARD => __('Mastercard'),
            self::VISA => __('VISA'),
            self::UNAVAILABLE => __('n/a'),
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    public function getBrandTranslate($code)
    {
        $brands = $this->getBrands();
        $brand = '';

        if (isset($brands[$code])) {
            $brand = $brands[$code];
        }

        return $brand;
    }

    /**
     * @return array
     */
    public function getStatus3ds()
    {
        return [
            self::NO_SECURE => __('No Secure'),
            self::LOW_RISK => __('Low Risk'),
            self::UNABLE_BUT_AUTHENTICATE => __('Unable but Authenticate'),
            self::HIGH_RISK => __('High Risk'),
            self::CRITIC_RISK => __('Critical Risk'),
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    public function getStatus3dsTranslate($code)
    {
        $status = $this->getStatus3ds();
        $state = '';

        if (isset($status[$code])) {
            $state = $status[$code];
        }

        return $state;
    }

    /**
     * @return array
     */
    public function getRefusals()
    {
        return [
            self::NEED_INFO => __('Need Infos'),
            self::REFUSED => __('Refused'),
            self::FORBIDDEN => __('Forbidden'),
            self::BLOCKED_FRAUD_FILTER => __('Blocked by the Fraud Filter'),
            self::BLOCKED_FRAUD_SCORING => __('Blocked by the Fraud Scoring'),
            self::FAIL_AUTHENTICATE => __('Failed to Authenticate'),
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    public function getRefusalTranslate($code)
    {
        $refusals = $this->getRefusals();
        $refusal = '';

        if (isset($refusals[$code])) {
            $refusal = $refusals[$code];
        }

        return $refusal;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isCryptoGiven($value)
    {
        return self::OUI === $value;
    }

    /**
     * @param $value
     * @return bool
     */
    public function isAllowedBrand($value)
    {
        return array_key_exists($value, $this->getBrands());
    }

    /**
     * Check if the payment has failed/canceled
     *
     * @param string $value
     * @return bool
     */
    public function isPaymentCanceled($value)
    {
        return self::ANNULATION === $value;
    }

    /**
     * Check the payment environment
     *
     * @param string $value
     * @return bool
     */
    public function isPaymentTest($value)
    {
        return self::PAYTEST === $value;
    }

    /**
     * Check if the payment is succeeded
     *
     * @param string $value
     * @return bool
     */
    public function isPaymentSucceed($value)
    {
        return self::PAIEMENT === $value;
    }
}
