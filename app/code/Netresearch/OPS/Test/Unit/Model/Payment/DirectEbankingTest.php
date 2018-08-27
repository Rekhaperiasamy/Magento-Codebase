<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment;

class DirectEbankingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\DirectEbanking
     */
    private $model;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Info
     */
    private $paymentInfo;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentInfo = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', [], [], '', false, false);
        $this->checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\DirectEbanking',
            ['checkoutSession' => $this->checkoutSession]
        );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testAssignData()
    {
        $this->paymentInfo->expects($this->at(0))->method('setAdditionalInformation')->with(
            $this->equalTo('PM'),
            $this->equalTo('DirectEbanking')
        );
        $this->paymentInfo->expects($this->at(1))->method('setAdditionalInformation')->with(
            $this->equalTo('BRAND'),
            $this->equalTo('DirectEbanking')
        );
        $this->paymentInfo->expects($this->any())
                          ->method('getAdditionalInformation')
                          ->will($this->returnValue('DirectEbanking'));
        $data = new \Magento\Framework\DataObject(['additional_data' => ['directEbanking_brand' => 'Sofort Uberweisung']]);
        $this->model->assignData($data);
        $this->assertEquals($this->model->getOpsBrand(), 'DirectEbanking');
        $this->assertEquals($this->model->getOpsCode(), 'DirectEbanking');
    }
}
