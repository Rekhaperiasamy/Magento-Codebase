<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment;

    /**
     * @category   OPS
     * @package    Netresearch_OPS
     * @author     Thomas Birke <thomas.birke@netresearch.de>
     * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
     * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
/**
 * \Netresearch\OPS\Test\Unit\Model\Payment\InterSolveTest
 *
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InterSolveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\InterSolve
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
        $this->objectManager   = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentInfo     = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', [], [], '', false, false);
        $this->checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->model           = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\InterSolve',
            ['checkoutSession' => $this->checkoutSession]
        );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testPm()
    {
        $this->paymentInfo->expects($this->any())
                          ->method('getAdditionalInformation')
                          ->will($this->returnValue('InterSolve'));
        $this->assertEquals('InterSolve', $this->model->getOpsCode());
    }

    public function testBrand()
    {
        $this->paymentInfo->expects($this->any())
                          ->method('getAdditionalInformation')
                          ->will($this->returnValue('InterSolve'));
        $this->assertEquals('InterSolve', $this->model->getOpsBrand());
    }

    public function testAssignDataWithBrand()
    {
        $this->paymentInfo->expects($this->any())->method('setAdditionalInformation')->with(
            $this->equalTo('BRAND'),
            $this->equalTo('FooBar')
        );
        $quote = new \Magento\Framework\DataObject(['payment' => $this->paymentInfo]);
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $data = new \Magento\Framework\DataObject(['additional_data' => ['intersolve_brand' => 'FooBar']]);
        $this->assertEquals('InterSolve', $this->model->getOpsCode());
        $method = $this->model->assignData($data);
        $this->assertInstanceOf('\Netresearch\OPS\Model\Payment\InterSolve', $method);
        $this->paymentInfo->expects($this->any())->method('getAdditionalInformation')->will($this->returnValue('FooBar'));
        $this->assertEquals('FooBar', $this->model->getOpsBrand());
    }

    public function testAssignDataWithoutBrand()
    {
        $this->paymentInfo->expects($this->any())->method('setAdditionalInformation')->with(
            $this->equalTo('BRAND'),
            $this->equalTo('InterSolve')
        );
        $quote = new \Magento\Framework\DataObject(['payment' => $this->paymentInfo]);
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
        $this->assertEquals('InterSolve', $this->model->getOpsCode());
        $this->model->assignData(new \Magento\Framework\DataObject());
        $this->assertEquals('InterSolve', $this->model->getOpsBrand());
    }
}
