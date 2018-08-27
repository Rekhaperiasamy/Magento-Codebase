<?php
/**
 * Netresearch OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Model\Payment\Features;

/**
 * Implements functionality to send Ingenico ePayments specific mails
 *
 * @category Payment method
 * @package  Netresearch OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
class PaymentEmail
{
    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    protected $oPSOrderHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlBuilder;

    public function __construct(
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Url $urlBuilder
    ) {
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->oPSPaymentHelper = $oPSPaymentHelper;
        $this->scopeConfig = $scopeConfig;
        $this->oPSOrderHelper = $oPSOrderHelper;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->urlBuilder = $urlBuilder;
    }

    protected function getConfig()
    {
        return $this->oPSConfigFactory->create();
    }

    /**
     * Check if payment email is available for order
     *
     * @param $order
     * @return bool
     */
    public function isAvailableForOrder($order)
    {
        if ($order instanceof \Magento\Sales\Model\Order) {
            $status = $order->getPayment()->getAdditionalInformation('status');

            return \Netresearch\OPS\Model\Status::canResendPaymentInfo($status);
        }

        return false;
    }

    /**
     * Resends the payment information and returns true/false, depending if succeeded or not
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return void
     */
    public function resendPaymentInfo(\Magento\Sales\Model\Order $order)
    {
        // reset payment method so the customer can choose freely from all available methods
        $this->setPaymentMethodToGeneric($order);

        $parameters = [
            "order" => $order,
            "paymentLink" => $this->generatePaymentLink($order),
            "store"       => $this->storeManager->getStore($order->getStoreId())
        ];


        if ($order->getPayment()->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\PayPerMail) {
            $templateId =  $this->getConfig()->getPayPerMailTemplate($order->getStoreId());
        } else {
            $templateId = $this->getConfig()->getResendPaymentInfoTemplate($order->getStoreId());
        }

        $identity = $this->getIdentity($this->getConfig()->getResendPaymentInfoIdentity($order->getStoreId()));

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $order->getStoreId()
                ]
            )
            ->setTemplateVars($parameters)
            ->setFrom(['email' => $identity->getEmail(), 'name' => $identity->getName()])
            ->addTo($order->getCustomerEmail(), $order->getCustomerName())
            ->getTransport();

        $transport->sendMessage();
    }

    /**
     * Generates the payment url
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function generatePaymentLink(\Magento\Sales\Model\Order $order)
    {
        $opsOrderId = $this->oPSOrderHelper->getOpsOrderId($order);

        $params = [
            'orderID' => $opsOrderId
        ];

        $secret = $this->getConfig()->getShaInCode($order->getStoreId());
        $raw = $this->oPSPaymentHelper->getSHAInSet($params, $secret);

        $params['SHASIGN'] = strtoupper($this->oPSPaymentHelper->shaCrypt($raw));

        $url = $this->getConfig()->getPaymentRetryUrl($params, $order->getStoreId());

        return $url;
    }

    /**
     * Set payment method to \Netresearch\OPS\Model\Payment\Flex
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @throws \Exception
     */
    protected function setPaymentMethodToGeneric(\Magento\Sales\Model\Order $order)
    {
        if (!$order->getPayment()->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\PayPerMail) {
            $order->getPayment()->setMethod(\Netresearch\OPS\Model\Payment\Flex::CODE)->save();
        }
    }

    /**
     * Loads email and name of the given store identity
     *
     * @param string $key - identity to load, defaults to sales
     *
     * @return \Magento\Framework\DataObject with data name and email
     */
    protected function getIdentity($key = 'sales')
    {
        $identity = new \Magento\Framework\DataObject();
        $identity->setName(
            $this->scopeConfig->getValue(
                'trans_email/ident_' . $key . '/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )
            ->setEmail(
                $this->scopeConfig->getValue(
                    'trans_email/ident_' . $key . '/email',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

        return $identity;
    }
}
