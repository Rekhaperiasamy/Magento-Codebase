<?php
namespace Netresearch\OPS\Test\Unit\Model\Backend\Operation\Capture;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch_OPS
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Capture\Parameter
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Netresearch\OPS\Helper\Order\Capture
     */
    private $orderHelper;

    private $config;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Capture\Additional\OpenInvoiceNl
     */
    private $backendOperationCaptureAdditionalOpenInvoiceNl;


    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->dataHelper    = $this->getMock('\Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $this->dataHelper->expects($this->any())->method('getAmount')->will($this->returnCallback(function ($v) {
            return $v * 100;
        }));
        $this->storeManager = $this->getMock('\Magento\Store\Model\StoreManager', [], [], '', false, false);
        $this->orderHelper  = $this->getMock('\Netresearch\OPS\Helper\Order\Capture', [], [], '', false, false);
        $this->config       = new \Magento\Framework\DataObject();
        $configFactory      = $this->getMock('Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())->method('create')->will($this->returnValue($this->config));
        $this->backendOperationCaptureAdditionalOpenInvoiceNl
            = $this->getMock(
                '\Netresearch\OPS\Model\Backend\Operation\Capture\Additional\OpenInvoiceNl',
                null,
                [],
                '',
                false,
                false
            );
        $backendOperationCaptureAdditionalOpenInvoiceNlFactory
            = $this->getMock(
                '\Netresearch\OPS\Model\Backend\Operation\Capture\Additional\OpenInvoiceNlFactory',
                [],
                [],
                '',
                false,
                false
            );
        $backendOperationCaptureAdditionalOpenInvoiceNlFactory->expects($this->any())
                                                              ->method('create')
                                                              ->will($this->returnValue($this->backendOperationCaptureAdditionalOpenInvoiceNl));
        $this->model
            = $this->objectManager->getObject(
                '\Netresearch\OPS\Model\Backend\Operation\Capture\Parameter',
                [
                                                  'oPSHelper'                                                => $this->dataHelper,
                                                  'storeManager'                                             => $this->storeManager,
                                                  'oPSOrderCaptureHelper'                                    => $this->orderHelper,
                                                  'oPSConfigFactory'                                         => $configFactory,
                                                  'oPSBackendOperationCaptureAdditionalOpenInvoiceNlFactory' => $backendOperationCaptureAdditionalOpenInvoiceNlFactory
                                              ]
            );
    }

    public function testGetRequestParams()
    {
        $fakePayment = new \Magento\Framework\DataObject();
        $fakePayment->setOrder(new \Magento\Framework\DataObject(['store_id' => 1]));
        $fakePayment->setAdditionalInformation(['paymentId' => '4711']);
        $arrInfo          = ['operation' => 'capture'];
        $amount           = 10;
        $opsPaymentMethod = $this->getMock('Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $this->orderHelper->expects($this->any())
                          ->method('determineOperationCode')
                          ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_PARTIAL));
        $this->storeManager->expects($this->any())
                           ->method('getStore')
                           ->will($this->returnValue(new \Magento\Framework\DataObject(['base_currency_code' => 'EUR'])));
        $this->config->setMethodsRequiringAdditionalParametersFor([\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_TRANSACTION_TYPE => []]);
        $requestParams = $this->model->getRequestParams($opsPaymentMethod, $fakePayment, $amount, $arrInfo);
        $this->assertArrayHasKey('AMOUNT', $requestParams);
        $this->assertArrayHasKey('PAYID', $requestParams);
        $this->assertArrayHasKey('OPERATION', $requestParams);
        $this->assertArrayHasKey('CURRENCY', $requestParams);
        $this->assertEquals(1000, $requestParams['AMOUNT']);
        $this->assertEquals(4711, $requestParams['PAYID']);
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_PARTIAL,
            $requestParams['OPERATION']
        );
        $this->assertEquals('EUR', $requestParams['CURRENCY']);
    }

    public function testGetRequestParamsWithAdditionalParameters()
    {
        $fakePayment = new \Magento\Framework\DataObject();
        $fakePayment->setOrder(new \Magento\Framework\DataObject(['store_id' => 1]));
        $fakePayment->setAdditionalInformation(['paymentId' => '4711']);
        $fakeInvoice = $this->getMock('\Magento\Sales\Model\Order\Invoice', [], [], '', false, false);
        $fakeInvoice->expects($this->any())->method('getItemsCollection')->will($this->returnValue([]));
        $fakePayment->setInvoice($fakeInvoice);
        $arrInfo          = ['operation' => 'capture'];
        $amount           = 10;
        $opsPaymentMethod = $this->getMock('\Netresearch\OPS\Model\Payment\OpenInvoiceNl', [], [], '', false, false);
        $this->orderHelper->expects($this->any())
                          ->method('determineOperationCode')
                          ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_PARTIAL));
        $this->storeManager->expects($this->any())
                           ->method('getStore')
                           ->will($this->returnValue(new \Magento\Framework\DataObject(['base_currency_code' => 'EUR'])));
        $this->config->setMethodsRequiringAdditionalParametersFor([
                                                                      \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_TRANSACTION_TYPE => [
                                                                          get_class($opsPaymentMethod)
                                                                      ]
                                                                  ]);
        $requestParams = $this->model->getRequestParams($opsPaymentMethod, $fakePayment, $amount, $arrInfo);
        $this->assertArrayHasKey('AMOUNT', $requestParams);
        $this->assertArrayHasKey('PAYID', $requestParams);
        $this->assertArrayHasKey('OPERATION', $requestParams);
        $this->assertArrayHasKey('CURRENCY', $requestParams);
        $this->assertEquals(1000, $requestParams['AMOUNT']);
        $this->assertEquals(4711, $requestParams['PAYID']);
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_PARTIAL,
            $requestParams['OPERATION']
        );
        $this->assertEquals('EUR', $requestParams['CURRENCY']);
    }
}
