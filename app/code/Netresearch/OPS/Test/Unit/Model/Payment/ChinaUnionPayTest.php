<?php

namespace Netresearch\OPS\Test\Unit\Model\Payment;

class ChinaUnionPayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\ChinaUnionPay
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
            '\Netresearch\OPS\Model\Payment\ChinaUnionPay',
            ['scopeConfig' => $this->config]
        );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    /**
     * assure that CUP can not capture partial, because invoice is always created on feedback in this case
     */
    public function testCanCapturePartial()
    {
        $this->assertFalse($this->model->canCapturePartial());
    }

    public function testGetOpsCode()
    {
        $this->assertEquals('PAYDOL_UPOP', $this->model->getOpsCode());
    }

    public function testGetOpsBrand()
    {
        $this->assertEquals('UnionPay', $this->model->getOpsBrand());
    }


    public function testCanRefundPartialPerInvoice()
    {
        $this->assertFalse($this->model->canRefundPartialPerInvoice());
    }

    public function testGetPaymentAction()
    {
        $this->assertEquals(
            \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
            $this->model->getPaymentAction()
        );
    }
}
