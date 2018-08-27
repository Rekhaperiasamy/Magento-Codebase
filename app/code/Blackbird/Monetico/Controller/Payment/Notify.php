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
namespace Blackbird\Monetico\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Sales\Model\Order;
use Blackbird\Monetico\Model\ConfigProvider;
use Blackbird\Monetico\Model\Config\Source\PostDataKey;
use Blackbird\Monetico\Model\MoneticoFactory;
use Blackbird\Monetico\Model\Monetico;
use Blackbird\Monetico\Model\Debug as DebugLog;

/**
 * @todo refactor
 */
class Notify extends \Magento\Framework\App\Action\Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var ConfigProvider
     */
    protected $_config;

    /**
     * @var PostDataKey
     */
    protected $_moneticoNotifyPostDataKey;

    /**
     * @var Monetico
     */
    protected $_monetico;

    /**
     * @var DebugLog
     */
    protected $_debugLog;

    /**
     * @var array
     */
    protected $moneticoPostValues = [];

    /**
     * @var string
     */
    protected $notifyResponse;

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param ConfigProvider $config
     * @param PostDataKey $postDataKey
     * @param MoneticoFactory $monetico
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        ConfigProvider $config,
        PostDataKey $postDataKey,
        MoneticoFactory $monetico,
        DebugLog $debugLog
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->_config = $config;
        $this->_moneticoNotifyPostDataKey = $postDataKey;
        $this->_monetico = $monetico->create();
        $this->_debugLog = $debugLog;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $this->notifyAction();
            } else {
                return $this->resultRedirectFactory->create()->setPath('/');
            }
        } catch (\Exception $e) {
            $this->_debugLog->addDebugMessage($e->getMessage(), $e->getTrace());
            $this->generateErrorResponse();
        }

        return $this->resultRawFactory->create()->setContents($this->notifyResponse);
    }

    /**
     * Monetico response : payment result
     *
     * @return $this
     */
    protected function notifyAction()
    {
        if (!$this->hasRequiredRequestData()) {
            $this->_debugLog->addDebugMessage(__('Some required parameters are missing.'));

            return $this->generateErrorResponse();
        }

        // Load and continue the order process
        if (!$this->_monetico->loadOrder($this->getMoneticoPostValue('reference'))) {
            $this->_debugLog->addDebugMessage(__('The order %1 does not exists.', $this->getMoneticoPostValue('reference')));

            return $this->generateErrorResponse();
        }

        if ($this->isValidMAC()) {
            $this->_config->setMethodCode($this->_monetico->getOrderMethod());
            $this->_debugLog->addDebugMessage(__('The order %1 has a valid MAC.', $this->getMoneticoPostValue('reference')));

            // Payment has been accepted
            if ($this->isPaymentSucceed()) {
                $this->_monetico->unholdOrder();
                $this->_debugLog->addDebugMessage(__('The payment of the order %1 has been validated.', $this->getMoneticoPostValue('reference')));
                $createInvoice = (bool)$this->_config->getSystemConfigValue('create_invoice');

                if ($this->_monetico->isOrderProcessing()) {
                    if ($createInvoice) {
                        $this->_monetico->saveInvoice();
                    }
                    $status = Order::STATE_PROCESSING;
                } elseif ($this->_monetico->isOrderComplete()) {
                    $this->_monetico->saveInvoice();
                    $this->_monetico->saveShipment();
                    $status = Order::STATE_COMPLETE;
                } else {
                    if ($createInvoice) {
                        $this->_monetico->saveInvoice();
                    }
                    if (($status = $this->_config->getSystemConfigValue('order_status_payment_accepted')) === null) {
                        $status = $this->_monetico->getOrderStatus();
                    };
                }

                $this->_monetico->updateOrder($this->getSuccessfulPaymentMessage(), $status, true);
                $this->_debugLog->addDebugMessage(__('The status of the order %1 is %2.', $this->getMoneticoPostValue('reference'), $status));

                // Response success curl
                return $this->generateSuccessResponse();

                // Payment has been rejected
            } elseif ($this->isPaymentRefused()) {
                $this->_monetico->unholdOrder();
                $this->_debugLog->addDebugMessage(__('The payment has been refused. Order: %1', $this->getMoneticoPostValue('reference')));

                if (($status = $this->_config->getSystemConfigValue('order_status_payment_refused')) === null) {
                    $status = $this->_monetico->getOrderStatus();
                };

                $this->_monetico->holdOrCancelOrder($status);
                $this->_monetico->updateOrder($this->getRefusedPaymentMessage());
                $this->_debugLog->addDebugMessage(__('The order %1 has been canceled or held.', $this->getMoneticoPostValue('reference')));

                // Response success curl
                return $this->generateSuccessResponse();
            }

            // Suspect fraud
        } else {
            $this->_monetico->cancelOrder();
            $this->_monetico->updateOrder(__('Returned MAC is invalid. Order canceled.'));
            $this->_debugLog->addDebugMessage(__('Returned MAC is invalid. Order %1 canceled.', $this->getMoneticoPostValue('reference')));
        }

        // Response failure curl
        return $this->generateErrorResponse();
    }

    /**
     * Retrieve the succeed payment message
     *
     * @return string
     */
    protected function getSuccessfulPaymentMessage()
    {
        $msg = __('Payment accepted by Monetico');
        $msg .= '<br />' . __('Used payment method: %1', $this->getMoneticoPostValue('modepaiement'));
        $msg .= '<br />' . __(
                'Captured amount: %1',
                !empty($this->getMoneticoPostValue('montantech')) ?
                    $this->getMoneticoPostValue('montantech') : $this->getMoneticoPostValue('montant')
            );
        $msg .= '<br />' . __('Authorization number: %1', $this->getMoneticoPostValue('numauto'));
        $msg .= '<br />' . __(
                '3D secure status: %1',
                $this->_monetico->getStatus3dsTranslate($this->getMoneticoPostValue('status3ds'))
            );
        $msg .= '<br />' . __(
                'The visual cryptogram has been seized: %1',
                $this->_monetico->getCvxTranslate($this->getMoneticoPostValue('cvx'))
            );
        $msg .= '<br />' . __('CC validity: %1', $this->getMoneticoPostValue('vld'));
        $msg .= '<br />' . __('CC type: %1', $this->_monetico->getBrandTranslate($this->getMoneticoPostValue('brand')));
        $msg .= '<br />' . __('Country code from the CC: %1', $this->getMoneticoPostValue('originecb'));
        $msg .= '<br />' . __('BIN code: %1', $this->getMoneticoPostValue('bincb'));

        return $msg;
    }

    /**
     * Retrieve the refused payment message
     *
     * @return string
     */
    protected function getRefusedPaymentMessage()
    {
        $msg = __('Payment refused by Monetico');
        $msg .= '<br />' . __('Used payment method: %1', $this->getMoneticoPostValue('modepaiement'));
        $msg .= '<br />' . __(
                'Captured amount: %1',
                !empty($this->getMoneticoPostValue('montantech')) ?
                    $this->getMoneticoPostValue('montantech') : $this->getMoneticoPostValue('montant')
            );
        $msg .= '<br />' . __(
                'Refusal reason: %1',
                $this->_monetico->getRefusalTranslate($this->getMoneticoPostValue('motifrefus'))
            );
        $msg .= '<br />' . __(
                '3D secure status: %1',
                $this->_monetico->getStatus3dsTranslate($this->getMoneticoPostValue('status3ds'))
            );
        $msg .= '<br />' . __(
                'The visual cryptogram has been seized: %1',
                $this->_monetico->getCvxTranslate($this->getMoneticoPostValue('cvx'))
            );
        $msg .= '<br />' . __('CC validity: %1', $this->getMoneticoPostValue('vld'));
        $msg .= '<br />' . __('CC type: %1', $this->_monetico->getBrandTranslate($this->getMoneticoPostValue('brand')));
        $msg .= '<br />' . __('Country code from the CC: %1', $this->getMoneticoPostValue('originecb'));
        $msg .= '<br />' . __('BIN code: %1', $this->getMoneticoPostValue('bincb'));

        return $msg;
    }

    /**
     * Generate the Success Response
     *
     * @return $this
     */
    protected function generateSuccessResponse()
    {
        $this->notifyResponse = implode("\n", ['version=2', 'cdr=0']) . "\n";

        return $this;
    }

    /**
     * Generate the Error Response
     *
     * @return $this
     */
    protected function generateErrorResponse()
    {
        $this->notifyResponse = implode("\n", ['version=2', 'cdr=1']) . "\n";

        return $this;
    }

    /**
     * Check if the current MAC, and returned by Monetico, are equals
     *
     * @return bool
     */
    protected function isValidMAC()
    {
        return $this->getMoneticoPostValue('MAC') === $this->_monetico->getOrderResponseMAC($this->getMoneticoPostValues());
    }

    /**
     * Check if the current payment is succeed
     *
     * @return bool
     */
    protected function isPaymentSucceed()
    {
        $status = $this->getMoneticoPostValue('code-retour');

        if ($this->_config->isSandbox()) {
            $success = $this->_monetico->isPaymentTest($status);
        } else {
            $success = $this->_monetico->isPaymentSucceed($status);
        }

        return $success;
    }

    /**
     * Check if the current payment is refused
     *
     * @return bool
     */
    protected function isPaymentRefused()
    {
        return $this->_monetico->isPaymentCanceled($this->getMoneticoPostValue('code-retour'));
    }

    /**
     * Check if all required data exists
     *
     * @return bool
     */
    protected function hasRequiredRequestData()
    {
        return (!empty($this->getMoneticoPostValue('reference')) && !empty($this->getMoneticoPostValue('MAC')));
    }

    /**
     * Retrieve the available request param values for Monetico
     *
     * @return array
     */
    protected function getMoneticoPostValues()
    {
        if (!$this->moneticoPostValues) {
            $values = [];

            foreach ($this->_moneticoNotifyPostDataKey->getOptions() as $key) {
                if (!is_null($this->getRequest()->getPostValue($key))) {
                    $values[$key] = $this->getRequest()->getPost($key);
                }
            }

            $this->moneticoPostValues = $values;
        }

        return $this->moneticoPostValues;
    }

    /**
     * Get param value from key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @deprecated
     */
    protected function getMoneticoPostValue($key, $default = '')
    {
        return $this->getRequest()->getPost($key, $default);
    }
}
