<?php
namespace Netresearch\OPS\Test\Unit\Model\Source\Cc;

class AliasInterfaceEnabledTypesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Source\Cc\Types
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->config        = new \Magento\Framework\DataObject(['all_cc_types' => ['VISA']]);
        $configFactory       = $this->getMock('Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())->method('create')->will($this->returnValue($this->config));
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Source\Cc\Types',
            ['oPSConfigFactory' => $configFactory]
        );
    }

    public function testGetAliasBrands()
    {
        $this->assertContains(['label' => 'VISA', 'value' => 'VISA'], $this->model->toOptionArray());
    }
}
