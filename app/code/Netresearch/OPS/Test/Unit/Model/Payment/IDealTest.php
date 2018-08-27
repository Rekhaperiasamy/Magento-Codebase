<?php

namespace Netresearch\OPS\Test\Unit\Model\Payment;

/**
 * @author      Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class IDealTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\IDeal
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

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentInfo     = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', [], [], '', false, false);
        $this->checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->scopeConfig = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->model         = $this->objectManager
            ->getObject(
                '\Netresearch\OPS\Model\Payment\IDeal',
                [
                    'checkoutSession' => $this->checkoutSession,
                    'scopeConfig' => $this->scopeConfig
                ]
            );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testGetIdealIssuers()
    {
        $issuers = ['ABNANL2A' => 'ABN AMRO'];
        $this->scopeConfig->expects($this->once())->method('getValue')->will($this->returnValue($issuers));
        $actualIssuers = $this->model->getIDealIssuers();
        $this->assertEquals($issuers, $actualIssuers);
    }

    public function testAssignData()
    {
        $this->paymentInfo->expects($this->any())
            ->method('setAdditionalInformation')
            ->with($this->equalTo('iDeal_issuer_id'), $this->equalTo('RBRBNL21'));

        $quote = new \Magento\Framework\DataObject(['payment' => $this->paymentInfo]);
        $this->checkoutSession->expects($this->any())->method('getQuote')->will($this->returnValue($quote));

        $data = new \Magento\Framework\DataObject(['iDeal_issuer_id' => 'RBRBNL21']);
        $this->assertEquals('iDEAL', $this->model->getOpsCode());

        $method = $this->model->assignData($data);
        $this->assertInstanceOf('\Netresearch\OPS\Model\Payment\IDeal', $method);
    }
}
