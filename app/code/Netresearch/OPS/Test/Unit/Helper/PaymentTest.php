<?php
namespace Netresearch\OPS\Test\Unit\Helper;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $helper;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    private $invoiceManagment;

    /**
     * @var \Netresearch\OPS\Model\Response\Handler
     */
    private $responseHandler;

    /**
     * @var \Netresearch\OPS\Model\Payment\CcFactory
     */
    private $paymentCcFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    private $dataHelper;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager    = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->config           = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->invoiceManagment = $this->getMock(
            '\Magento\Sales\Model\Service\InvoiceService',
            [],
            [],
            '',
            false,
            false
        );
        $this->responseHandler  = $this->getMock('\Netresearch\OPS\Model\Response\Handler', [], [], '', false, false);
        $this->paymentCcFactory = $this->getMock('\Netresearch\OPS\Model\Payment\CcFactory', [], [], '', false, false);
        $this->registry         = $this->getMock('\Magento\Framework\Registry', [], [], '', false, false);
        $this->dataHelper       = $this->getMock('\Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $this->helper           = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Payment',
            [
                                                                      'oPSConfig'           => $this->config,
                                                                      'invoiceManagement'   => $this->invoiceManagment,
                                                                      'responseHandler'     => $this->responseHandler,
                                                                      'oPSPaymentCcFactory' => $this->paymentCcFactory,
                                                                      'registry'            => $this->registry,
                                                                      'oPSHelper'           => $this->dataHelper
                                                                  ]
        );
    }

    public function testIsPaymentAuthorizeType()
    {
        $this->assertTrue($this->helper->isPaymentAuthorizeType(\Netresearch\OPS\Model\Status::AUTHORIZED));
        $this->assertTrue($this->helper->isPaymentAuthorizeType(\Netresearch\OPS\Model\Status::AUTHORIZATION_WAITING));
        $this->assertTrue($this->helper->isPaymentAuthorizeType(\Netresearch\OPS\Model\Status::AUTHORIZED_UNKNOWN));
        $this->assertTrue($this->helper->isPaymentAuthorizeType(\Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT));
        $this->assertFalse($this->helper->isPaymentAuthorizeType(0));
    }

    public function testIsPaymentCaptureType()
    {
        $this->assertTrue($this->helper->isPaymentCaptureType(\Netresearch\OPS\Model\Status::PAYMENT_REQUESTED));
        $this->assertTrue($this->helper->isPaymentCaptureType(\Netresearch\OPS\Model\Status::PAYMENT_PROCESSING));
        $this->assertTrue($this->helper->isPaymentCaptureType(\Netresearch\OPS\Model\Status::PAYMENT_UNCERTAIN));
        $this->assertFalse($this->helper->isPaymentCaptureType(0));
    }

    /**
     * send no invoice mail if it is denied by configuration
     */
    public function testSendNoInvoiceToCustomerIfDenied()
    {
        $this->config->expects($this->any())->method('getSendInvoice')->will($this->returnValue(0));
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->getMock('\Magento\Sales\Model\Order\Invoice', [], [], '', false, false);
        $invoice->expects($this->any())->method('getEmailSent')->will($this->returnValue(false));
        $invoice->expects($this->never())->method('getEntityId');
        $this->helper->sendInvoiceToCustomer($invoice);
    }

    /**
     * send no invoice mail if it was already sent
     */
    public function testSendNoInvoiceToCustomerIfAlreadySent()
    {
        $this->config->expects($this->any())->method('getSendInvoice')->will($this->returnValue(1));
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->getMock('\Magento\Sales\Model\Order\Invoice', [], [], '', false, false);
        $invoice->expects($this->any())->method('getEmailSent')->will($this->returnValue(true));
        $invoice->expects($this->never())->method('getEntityId');
        $this->helper->sendInvoiceToCustomer($invoice);
    }

    /**
     * send invoice mail
     */
    public function testSendInvoiceToCustomerIfEnabled()
    {
        $this->config->expects($this->any())->method('getSendInvoice')->will($this->returnValue(1));
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->getMock('\Magento\Sales\Model\Order\Invoice', [], [], '', false, false);
        $invoice->expects($this->any())->method('getEmailSent')->will($this->returnValue(false));
        $invoice->expects($this->once())->method('getEntityId')->will($this->returnValue(1));
        $this->invoiceManagment->expects($this->once())->method('notify')->will($this->returnSelf());
        $this->helper->sendInvoiceToCustomer($invoice);
    }

    public function testPrepareParamsAndSort()
    {
        $this->config->expects($this->any())->method('getConfigData')->will($this->returnValue('sha1'));

        $params       = [
            'CVC'          => '123',
            'CARDNO'       => '4111111111111111',
            'CN'           => 'JohnSmith',
            'PSPID'        => 'test1',
            'ED'           => '1212',
            'ACCEPTURL'    => 'https=//www.myshop.com/ok.html',
            'EXCEPTIONURL' => 'https=//www.myshop.com/nok.html',
            'BRAND'        => 'VISA',
        ];
        $sortedParams = [
            'ACCEPTURL'    => [
                'key'   => 'ACCEPTURL',
                'value' => 'https=//www.myshop.com/ok.html'
            ],
            'BRAND'        => ['key' => 'BRAND', 'value' => 'VISA'],
            'CARDNO'       => [
                'key'   => 'CARDNO',
                'value' => '4111111111111111'
            ],
            'CN'           => ['key' => 'CN', 'value' => 'JohnSmith'],
            'CVC'          => ['key' => 'CVC', 'value' => '123'],
            'ED'           => ['key' => 'ED', 'value' => '1212'],
            'EXCEPTIONURL' => [
                'key'   => 'EXCEPTIONURL',
                'value' => 'https=//www.myshop.com/nok.html'
            ],
            'PSPID'        => ['key' => 'PSPID', 'value' => 'test1'],
        ];
        $secret       = 'Mysecretsig1875!?';
        $shaInSet
                      = 'ACCEPTURL=https=//www.myshop.com/ok.htmlMysecretsig1875!?BRAND=VISAMysecretsig1875!?'
                        . 'CARDNO=4111111111111111Mysecretsig1875!?CN=JohnSmithMysecretsig1875!?CVC=123Mysecretsig1875!?'
                        . 'ED=1212Mysecretsig1875!?EXCEPTIONURL=https=//www.myshop.com/nok.htmlMysecretsig1875!?'
                        . 'PSPID=test1Mysecretsig1875!?';
        $key          = 'a28dc9fe69b63fe81da92471fefa80aca3f4851a';
        $this->assertEquals(
            $sortedParams,
            $this->helper->prepareParamsAndSort($params)
        );
        $this->assertEquals(
            $shaInSet,
            $this->helper->getSHAInSet($params, $secret)
        );
        $this->assertEquals($key, $this->helper->shaCrypt($shaInSet, $secret));
    }

    public function testHandleUnknownStatus()
    {
        $order = $this->getMock('\Magento\Sales\Model\Order', ['save', 'addStatusHistoryComment', 'setIsCustomerNotified'], [], '', false, false);
        $order->expects($this->atLeastOnce())->method('save')->will($this->returnValue(true));
        $order->expects($this->atLeastOnce())->method('addStatusHistoryComment')->will($this->returnSelf());
        $order->expects($this->atLeastOnce())->method('setIsCustomerNotified')->with(false)->will($this->returnSelf());
        $order->setState(\Magento\Sales\Model\Order::STATE_NEW);
        $this->helper->handleUnknownStatus($order);
        $this->assertEquals(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT, $order->getState());
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
        $this->helper->handleUnknownStatus($order);
        $this->assertEquals(\Magento\Sales\Model\Order::STATE_PROCESSING, $order->getState());
    }
    
    public function testGetBaseGrandTotalFromSalesObject()
    {
        $baseGrandTotal = 145.09;
        $order  = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->once())->method('getBaseGrandTotal')->will($this->returnValue($baseGrandTotal));
        $amount = $this->helper->getBaseGrandTotalFromSalesObject($order);
        $this->assertEquals($baseGrandTotal, $amount);
        $quote = $this->getMock('\Magento\Quote\Model\Quote', ['getBaseGrandTotal'], [], '', false, false);
        $quote->expects($this->any())->method('getBaseGrandTotal')->will($this->returnValue($baseGrandTotal));
        $amount = $this->helper->getBaseGrandTotalFromSalesObject($quote);
        $this->assertEquals($baseGrandTotal, $amount);
        $someOtherObject = new \Magento\Framework\DataObject();
        $this->setExpectedException('\Magento\Framework\Exception\LocalizedException');
        $this->helper->getBaseGrandTotalFromSalesObject($someOtherObject);
    }
    
    public function testSaveOpsRefundOperationCodeToPayment()
    {
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save', 'getOrder'], [], '', false, false);
        $payment->expects($this->atLeastOnce())->method('save')->will($this->returnSelf());
        $payment->expects($this->atLeastOnce())
                ->method('getOrder')
                ->will($this->returnValue(new \Magento\Framework\DataObject(['increment_id' => 1000023])));
        // no last refund operation code is set if an empty string is passed
        $this->helper->saveOpsRefundOperationCodeToPayment($payment, '');
        $this->assertFalse(array_key_exists(
            'lastRefundOperationCode',
            $payment->getAdditionalInformation()
        ));
        // no last refund operation code is set if it's no refund operation code
        $this->helper->saveOpsRefundOperationCodeToPayment(
            $payment,
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_FULL
        );
        $this->assertFalse(array_key_exists(
            'lastRefundOperationCode',
            $payment->getAdditionalInformation()
        ));
        // last ops refund code is present if a valid refund code is passed
        $this->helper->saveOpsRefundOperationCodeToPayment(
            $payment,
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_PARTIAL
        );
        $this->assertTrue(array_key_exists(
            'lastRefundOperationCode',
            $payment->getAdditionalInformation()
        ));
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_PARTIAL,
            $payment->getAdditionalInformation('lastRefundOperationCode')
        );
        // last ops refund code is present if a valid refund code is passed and will override a previous one
        $this->helper->saveOpsRefundOperationCodeToPayment(
            $payment,
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL
        );
        $this->assertTrue(array_key_exists(
            'lastRefundOperationCode',
            $payment->getAdditionalInformation()
        ));
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL,
            $payment->getAdditionalInformation('lastRefundOperationCode')
        );
    }
    
    public function testSetCanRefundToPayment()
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save', 'getOrder'], [], '', false, false);
        $payment->expects($this->atLeastOnce())->method('save')->will($this->returnSelf());
        $payment->expects($this->atLeastOnce())
                ->method('getOrder')
                ->will($this->returnValue(new \Magento\Framework\DataObject(['increment_id' => 1000023])));

        $this->helper->setCanRefundToPayment($payment);
        $this->assertFalse(array_key_exists('canRefund', $payment->getAdditionalInformation()));
        $payment->setAdditionalInformation('lastRefundOperationCode', \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_PARTIAL);
        $this->helper->setCanRefundToPayment($payment);
        $this->assertTrue($payment->getAdditionalInformation('canRefund'));
        $payment->setAdditionalInformation('lastRefundOperationCode', \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_FULL);
        $this->helper->setCanRefundToPayment($payment);
        $this->assertFalse($payment->getAdditionalInformation('canRefund'));
    }

    public function testApplyStateForOrder()
    {
        $this->responseHandler->expects($this->atLeastOnce())->method('processResponse')->will($this->returnValue(true));
        $paymentInstance = $this->getMock('Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $payment->expects($this->atLeastOnce())->method('save')->will($this->returnSelf());
        $payment->expects($this->atLeastOnce())
                ->method('getMethodInstance')
                ->will($this->returnValue($paymentInstance));
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->atLeastOnce())->method('getPayment')->will($this->returnValue($payment));

        // assertion for OPS_OPEN_INVOICE_DE_PROCESSED = 41000001
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT]
            )
        );
        // assertion for WAITING_FOR_IDENTIFICATION  = 46
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::WAITING_FOR_IDENTIFICATION]
            )
        );
        // assertion for AUTHORIZED  = 5
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::AUTHORIZED]
            )
        );
        // assertion for AUTHORIZED_WAITING_EXTERNAL_RESULT  = 50
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::AUTHORIZED_WAITING_EXTERNAL_RESULT]
            )
        );
        // assertion for AUTHORIZATION_WAITING = 51
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::AUTHORIZATION_WAITING]
            )
        );
        // assertion for AUTHORIZED_UNKNOWN  = 52
        $this->assertEquals(
            'exception',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::AUTHORIZED_UNKNOWN]
            )
        );
        // assertion for WAITING_CLIENT_PAYMENT = 41
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT]
            )
        );
        // assertion for PAYMENT_REQUESTED = 9
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED]
            )
        );
        // assertion for PAYMENT_PROCESSING = 91
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_PROCESSING]
            )
        );
        // assertion for OPS_OPEN_INVOICE_DE_PROCESSED  = 41000001
        $this->assertEquals(
            'accept',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT]
            )
        );
        // assertion for AUTHORISATION_DECLINED   = 2
        $this->assertEquals(
            'decline',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED]
            )
        );
        // assertion for PAYMENT_REFUSED = 93
        $this->assertEquals(
            'decline',
            $this->helper->applyStateForOrder(
                $order,
                ['STATUS' => \Netresearch\OPS\Model\Status::PAYMENT_REFUSED]
            )
        );
        // assertion for CANCELED_BY_CUSTOMER        = 1
        $this->assertEquals(
            'cancel',
            $this->helper->applyStateForOrder(
                $order,
                [
                                                                  'STATUS' => \Netresearch\OPS\Model\Status::CANCELED_BY_CUSTOMER,
                                                                  'PAYID'  => 4711
                                                              ]
            )
        );
    }

    public function testAddCCForZeroAmountCheckout()
    {
        $block  = $this->objectManager->getObject('\Magento\Payment\Block\Form\Container');
        $method = new \Magento\Framework\DataObject();
        $method->setCode('ops_ideal');
        $block->setData('methods', [$method]);
        $featureModel = $this->getMock('\Netresearch\OPS\Model\Payment\Features\ZeroAmountAuth', [], [], '', false, false);
        $featureModel->expects($this->any())
                         ->method('isCCAndZeroAmountAuthAllowed')
                         ->will($this->returnValue(true));
        $paymentCc = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $paymentCc->expects($this->atLeastOnce())->method('getFeatureModel')->will($this->returnValue($featureModel));
        $this->paymentCcFactory->expects($this->atLeastOnce())->method('create')->will($this->returnValue($paymentCc));
        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->expects($this->any())->method('getPayment')->will($this->returnValue($payment));
        $block->setQuote($quote);
        $this->helper->addCCForZeroAmountCheckout($block);
        $methods = $block->getMethods();
        $this->assertTrue($methods[1] instanceof \Netresearch\OPS\Model\Payment\Cc);
        $this->assertFalse($methods[0] instanceof \Netresearch\OPS\Model\Payment\Cc);
    }

    public function testIsInlinePaymentWithOrderIdIsTrueForInlineCcWithOrderId()
    {
        $this->config->expects($this->any())
                   ->method('getInlineOrderReference')
                   ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID));
        $paymentCc = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $paymentCc->expects($this->once())->method('hasBrandAliasInterfaceSupport')->will($this->returnValue(true));
        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($paymentCc));
        $this->assertTrue($this->helper->isInlinePaymentWithOrderId($payment));
    }

    public function testIsInlinePaymentWithOrderIdIsFalseForRedirectCcWithOrderId()
    {
        $paymentCc = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $paymentCc->expects($this->any())->method('getConfigPaymentAction')->will($this->returnValue('authorize'));
        $paymentCc->expects($this->once())->method('hasBrandAliasInterfaceSupport')->will($this->returnValue(false));
        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($paymentCc));
        $this->assertFalse($this->helper->isInlinePaymentWithOrderId($payment));
    }

    public function testIsInlinePaymentWithOrderIdIsFalseIfQuoteIdIsConfigured()
    {
        $this->config->expects($this->any())
                   ->method('getInlineOrderReference')
                   ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID));
        $paymentCc = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $paymentCc->expects($this->once())->method('hasBrandAliasInterfaceSupport')->will($this->returnValue(true));
        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($paymentCc));
        $this->assertFalse($this->helper->isInlinePaymentWithOrderId($payment));
    }

    public function testIsInlinePaymentWithOrderIdIsFalseIfQuoteIdIsConfiguredForDirectDebit()
    {
        $this->config->expects($this->any())
                   ->method('getInlineOrderReference')
                   ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID));
        $paymentDd = $this->getMock('\Netresearch\OPS\Model\Payment\DirectDebit', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->any())
                ->method('getMethodInstance')
                ->will($this->returnValue($paymentDd));
        $this->assertFalse($this->helper->isInlinePaymentWithOrderId($payment));
    }

    public function testIsInlinePaymentWithOrderIdIsTrueIfOrderIdIsConfiguredForDirectDebit()
    {
        $this->config->expects($this->any())
                   ->method('getInlineOrderReference')
                   ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID));
        $paymentDd = $this->getMock('\Netresearch\OPS\Model\Payment\DirectDebit', [], [], '', false, false);
        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->any())
                ->method('getMethodInstance')
                ->will($this->returnValue($paymentDd));
        $this->assertTrue($this->helper->isInlinePaymentWithOrderId($payment));
    }

    public function testIsInlinePaymentWithQuoteId()
    {
        $paymentDd = $this->getMock('\Netresearch\OPS\Model\Payment\DirectDebit', [], [], '', false, false);
        $paymentDd->expects($this->once())->method('getConfigPaymentAction')->will($this->returnValue(''));
        $payment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $payment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($paymentDd));
        $this->assertTrue($this->helper->isInlinePaymentWithQuoteId($payment));
    }

    public function testSetInvoicesToPaid()
    {
        $invoice = $this->getMock('\Magento\Sales\Model\Order\Invoice', ['save'], [], '', false, false);
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())
              ->method('getInvoiceCollection')
              ->will($this->returnValue([$invoice]));
        $this->helper->setInvoicesToPaid($order);
        foreach ($order->getInvoiceCollection() as $invoice) {
            $this->assertEquals(\Magento\Sales\Model\Order\Invoice::STATE_PAID, $invoice->getState());
        }
    }
}
