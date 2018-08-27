<?php
namespace Netresearch\OPS\Test\Unit\Model\Source;

class OrderReferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Source\OrderReference
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Source\OrderReference');
    }

    public function testToOptionArray()
    {
        $options = $this->model->toOptionArray();
        $this->assertTrue(is_array($options));
        // check for the existence of the keys for order or quote id
        $this->assertEquals($options[0]['value'], \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_QUOTE_ID);
        $this->assertEquals($options[1]['value'], \Netresearch\OPS\Model\Payment\PaymentAbstract::REFERENCE_ORDER_ID);
    }
}
