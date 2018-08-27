<?php
namespace Netresearch\OPS\Test\Unit\Model\Source;

/**
 * ModeTest.php
 *
 * @author    paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 */
class ModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Source\Mode
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Source\Mode');
    }
    
    public function testToOptionArray()
    {
        $options = $this->model->toOptionArray();
        $this->assertTrue(is_array($options));
        $this->assertEquals(\Netresearch\OPS\Model\Source\Mode::TEST, $options[0]['value']);
        $this->assertEquals(\Netresearch\OPS\Model\Source\Mode::PROD, $options[1]['value']);
        $this->assertEquals(\Netresearch\OPS\Model\Source\Mode::CUSTOM, $options[2]['value']);
    }
}
