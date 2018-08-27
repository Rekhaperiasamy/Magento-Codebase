<?php

namespace Netresearch\OPS\Test\Unit\Block\Form;

class CcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }

    public function testGetAliasBrands()
    {
        $aliasBrands = [
            'American Express',
            'Diners Club',
            'MaestroUK',
            'MasterCard',
            'VISA',
        ];
        /** @var \Netresearch\OPS\Model\Source\Cc\AliasInterfaceEnabledTypesFactory $ccAliasInterfaceEnabledTypesMock */
        $ccAliasEnabledTypesFactoryMock = $this->getMock(
            'Netresearch\OPS\Model\Source\Cc\AliasInterfaceEnabledTypesFactory',
            ['create'],
            [],
            '',
            false,
            false
        );
        /** @var \Netresearch\OPS\Model\Source\Cc\AliasInterfaceEnabledTypes $ccAliasInterfaceEnabledTypesMock */
        $ccAliasInterfaceEnabledTypesMock = $this->getMock(
            'Netresearch\OPS\Model\Source\Cc\AliasInterfaceEnabledTypes',
            ['getAliasInterfaceCompatibleTypes'],
            [],
            '',
            false,
            false
        );
        $ccAliasEnabledTypesFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($ccAliasInterfaceEnabledTypesMock));
        $ccAliasInterfaceEnabledTypesMock->expects($this->any())
            ->method('getAliasInterfaceCompatibleTypes')
            ->will($this->returnValue($aliasBrands));
        /** @var \Netresearch\OPS\Block\Form\Cc $ccForm */
        $ccForm = $this->objectManager->getObject(
            'Netresearch\OPS\Block\Form\Cc',
            ['oPSSourceCcAliasEnabledTypesFactory' => $ccAliasEnabledTypesFactoryMock]
        );

        $this->assertEquals($aliasBrands, $ccForm->getAliasBrands());
    }


    public function testTemplate()
    {
        /** @var \Netresearch\OPS\Block\Form\Cc $ccForm */
        $ccForm = $this->objectManager->getObject('Netresearch\OPS\Block\Form\Cc');
        $this->assertEquals(\Netresearch\OPS\Block\Form\Cc::FRONTEND_TEMPLATE, $ccForm->getTemplate());
    }

    public function testGetCcBrands()
    {
        /** @var \Netresearch\OPS\Block\Form\Cc $blockMock */
        $blockMock = $this->getMock('Netresearch\OPS\Block\Form\Cc', ['getMethod', 'getConfig'], [], '', false, false);
        $configMock = $this->getMock('Netresearch\OPS\Model\Config', [], [], '', false, false);
        $configMock->expects($this->once())
            ->method('getAcceptedCcTypes')
            ->will($this->returnValue('VISA'));
        $method = new \Magento\Framework\DataObject();
        $method->setCode('ops_cc');
        $blockMock->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($method));
        $blockMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($configMock));

        $actual = $blockMock->getCcBrands();

        $this->assertInternalType('array', $actual);
        $this->assertEquals(['VISA'], $actual);
    }

    public function testIsAliasPMEnabled()
    {
        $configMock = $this->getMock('Netresearch\OPS\Model\Config', ['isAliasManagerEnabled'], [], '', false, false);
        $configMock->expects($this->once())
            ->method('isAliasManagerEnabled')
            ->with('ops_method')
            ->will($this->returnValue(true));
        /** @var \Netresearch\OPS\Block\Form\Cc $ccForm */
        $ccForm = $this->objectManager->getObject('Netresearch\OPS\Block\Form\Cc', ['oPSConfig' => $configMock]);
        $method = $this->getMockForAbstractClass('Magento\Payment\Model\MethodInterface');
        $method->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('ops_method'));
        $ccForm->setMethod($method);
        $this->assertEquals(true, $ccForm->isAliasPMEnabled());
    }
}
