<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment\Features;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ZeroAmountAuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\Features\ZeroAmountAuth
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Model\Payment\Cc
     */
    private $paymentCc;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentCc     = $this->getMock('\Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $this->quote         = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Payment\Features\ZeroAmountAuth');
    }

    public function testIsCCAndZeroAmountAuthAllowedNoCC()
    {
        $this->assertFalse($this->model->isCCAndZeroAmountAuthAllowed($this->paymentCc, $this->quote));
    }


    public function testIsCCAndZeroAmountAuthAllowedFalse()
    {
        $this->paymentCc->expects($this->once())
                        ->method('isZeroAmountAuthorizationAllowed')
                        ->will($this->returnValue(false));
        $this->assertFalse($this->model->isCCAndZeroAmountAuthAllowed($this->paymentCc, $this->quote));
    }


    public function testIsCCAndZeroAmountAuthAllowedTrue()
    {
        $this->paymentCc->expects($this->any())
                        ->method('isZeroAmountAuthorizationAllowed')
                        ->will($this->returnValue(true));
        $this->quote->expects($this->any())->method('getItemsCount')->will($this->returnValue(1));
        $this->quote->expects($this->any())->method('isNominal')->will($this->returnValue(false));
        $this->quote->expects($this->any())->method('getBaseGrandTotal')->will($this->returnValue(100));
        $this->assertTrue($this->model->isCCAndZeroAmountAuthAllowed($this->paymentCc, $this->quote));
    }
}
