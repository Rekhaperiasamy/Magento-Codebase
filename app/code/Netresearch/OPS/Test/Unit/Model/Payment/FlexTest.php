<?php

namespace Netresearch\OPS\Test\Unit\Model\Payment;

/**
 * Netresearch\OPS
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
 * @copyright Copyright (c) 2016 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

/**
 * FlexTest.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */


class FlexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\Flex
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
        $this->paymentInfo     = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', [], [], '', false, false);
        $this->checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Payment\Flex', ['checkoutSession' => $this->checkoutSession]);
        $this->model->setInfoInstance($this->paymentInfo);
    }

    protected function getAdditionalInfo()
    {
        return [
            \Netresearch\OPS\Model\Payment\Flex::INFO_KEY_TITLE => 'Foobar',
            \Netresearch\OPS\Model\Payment\Flex::INFO_KEY_PM    => 'foo',
            \Netresearch\OPS\Model\Payment\Flex::INFO_KEY_BRAND => 'bar'
        ];
    }

    public function testGetOpsCode()
    {
        $additionalInfo = $this->getAdditionalInfo();
        $this->paymentInfo->expects($this->any())
                          ->method('getAdditionalInformation')
                          ->will($this->returnValue($additionalInfo[\Netresearch\OPS\Model\Payment\Flex::INFO_KEY_PM]));

        $this->assertEquals($additionalInfo[\Netresearch\OPS\Model\Payment\Flex::INFO_KEY_PM], $this->model->getOpsCode());
    }

    public function testGetOpsBrand()
    {
        $additionalInfo = $this->getAdditionalInfo();
        $this->paymentInfo->expects($this->any())
                          ->method('getAdditionalInformation')
                          ->will($this->returnValue($additionalInfo[\Netresearch\OPS\Model\Payment\Flex::INFO_KEY_BRAND]));

        $this->assertEquals(
            $additionalInfo[\Netresearch\OPS\Model\Payment\Flex::INFO_KEY_BRAND],
            $this->model->getOpsBrand()
        );
    }
}
