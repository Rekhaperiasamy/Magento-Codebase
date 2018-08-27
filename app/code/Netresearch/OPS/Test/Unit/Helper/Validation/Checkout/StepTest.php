<?php

namespace Netresearch\OPS\Test\Unit\Helper\Validation\Checkout;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class StepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Validation\Checkout\Step
     */
    protected $stepHelper;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->stepHelper = $this->objectManager->getObject('\Netresearch\OPS\Helper\Validation\Checkout\Step');
    }

    public function testHelperReturnsNoStep()
    {
        $this->assertEquals('', $this->stepHelper->getStep([]));
        $this->assertEquals('', $this->stepHelper->getStep(['SOME_OTHER_FIELD']));
    }

    public function testHelperReturnsBillingStep()
    {
        $expectedStep = \Netresearch\OPS\Helper\Validation\Checkout\Step::BILLING_STEP;
        $this->assertEquals($expectedStep, $this->stepHelper->getStep(['OWNERADDRESS']));
        $this->assertEquals($expectedStep, $this->stepHelper->getStep(['OWNERADDRESS', 'SOME_OTHER_FIELD']));
        $this->assertEquals($expectedStep, $this->stepHelper->getStep(['ECOM_SHIPTO_POSTAL_STATE', 'SOME_OTHER_FIELD', 'CN']));
    }

    public function testHelperReturnsShippingStep()
    {
        $expectedStep = \Netresearch\OPS\Helper\Validation\Checkout\Step::SHIPPING_STEP;
        $this->assertEquals($expectedStep, $this->stepHelper->getStep(['ECOM_SHIPTO_POSTAL_STATE']));
        $this->assertEquals($expectedStep, $this->stepHelper->getStep(['ECOM_SHIPTO_POSTAL_STATE', 'SOME_OTHER_FIELD']));
    }
}
