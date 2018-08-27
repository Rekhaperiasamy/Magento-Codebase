<?php

namespace Netresearch\OPS\Test\Unit\Model\Payment;

class BancontactTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\Bancontact
     */
    private $model;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Info
     */
    private $paymentInfo;

    /**
     * @var \Magento\Framework\App\Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentInfo   = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', [], [], '', false, false);
        $this->config        = $this->getMock('\Magento\Framework\App\Config', [], [], '', false, false);
        $this->model         = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\Bancontact',
            ['scopeConfig' => $this->config]
        );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testCanCapturePartial()
    {
        $this->assertTrue($this->model->canCapturePartial());
    }

    public function testGetOpsCode()
    {
        $this->assertEquals('CreditCard', $this->model->getOpsCode());
    }

    public function testGetOpsBrand()
    {
        $this->assertEquals('BCMC', $this->model->getOpsBrand());
    }

    public function testGetMethodDependendFormFields()
    {
        /** @var \Magento\Sales\Model\Order\Payment $orderPayment */
        $orderPayment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $orderPayment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($this->model));

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false, false);
        $order->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($orderPayment));

        $this->paymentInfo->expects($this->any())
            ->method('getAdditionalInformation')
            ->will($this->returnValue(\Netresearch\OPS\Helper\MobileDetect::DEVICE_TYPE_MOBILE));

        $formFields = $this->model->getMethodDependendFormFields($order, null);
        $this->assertEquals(\Netresearch\OPS\Helper\MobileDetect::DEVICE_TYPE_MOBILE, $formFields['DEVICE']);
    }


    public function testAssignData()
    {
        $helperMock = new \Magento\Framework\DataObject(
            ['device_type' => \Netresearch\OPS\Helper\MobileDetect::DEVICE_TYPE_MOBILE]
        );

        $this->paymentInfo->expects($this->any())
            ->method('getAdditionalInformation')
            ->will($this->returnValue(['DEVICE' => \Netresearch\OPS\Helper\MobileDetect::DEVICE_TYPE_MOBILE]));

        $this->model->setMobileDetectHelper($helperMock);

        $this->model->assignData(new \Magento\Framework\DataObject());
        $additionalInformation = $this->paymentInfo->getAdditionalInformation();

        $this->assertEquals(\Netresearch\OPS\Helper\MobileDetect::DEVICE_TYPE_MOBILE, $additionalInformation['DEVICE']);
    }

    public function testGetMobileDetectHelper()
    {
        $this->assertTrue($this->model->getMobileDetectHelper() instanceof \Netresearch\OPS\Helper\MobileDetect);
    }
}
