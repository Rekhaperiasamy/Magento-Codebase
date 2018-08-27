<?php
namespace Netresearch\OPS\Test\Unit\Model\Source\DirectEbanking;

class BrandsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Source\DirectEbanking\Brands
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Source\DirectEbanking\Brands');
    }

    public function testToOptionArray()
    {
        $options = $this->model->toOptionArray();
        $this->assertTrue(is_array($options));
        $this->assertEquals($options[0]['value'], 'DirectEbanking');
        $this->assertEquals($options[1]['value'], 'DirectEbankingAT');
        $this->assertEquals($options[2]['value'], 'DirectEbankingBE');
        $this->assertEquals($options[3]['value'], 'DirectEbankingCH');
        $this->assertEquals($options[4]['value'], 'DirectEbankingDE');
        $this->assertEquals($options[5]['value'], 'DirectEbankingFR');
        $this->assertEquals($options[6]['value'], 'DirectEbankingGB');
    }
}
