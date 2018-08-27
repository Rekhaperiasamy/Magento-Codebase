<?php

namespace Netresearch\OPS\Test\Unit\Model\Payment\Backend;

class OpsIdTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\Backend\OpsId
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
            '\Netresearch\OPS\Model\Payment\Backend\OpsId',
            ['scopeConfig' => $this->config]
        );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testAssignData()
    {
        $paymentId = '12345689';
        $this->paymentInfo->expects($this->any())
                          ->method('getAdditionalInformation')
                          ->will($this->returnValue(['paymentId' => $paymentId]));

        $data = new \Magento\Framework\DataObject();
        $data->addData(['additional_data' => ['ops_pay_id' => $paymentId]]);
        $this->model->assignData($data);
        $additionalInformation = $this->paymentInfo->getAdditionalInformation();

        $this->assertEquals($paymentId, $additionalInformation['paymentId']);
    }
}
