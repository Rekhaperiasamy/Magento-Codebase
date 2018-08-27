<?php

namespace Netresearch\OPS\Test\Unit\Block\Form;

use Netresearch\OPS\Block\Form\DirectDebit;
use Netresearch\OPS\Model\Config;

class DirectDebitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    private $configMock;

    /**
     * @var DirectDebit
     */
    private $block;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['getDirectDebitCountryIds'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->block = $this->objectManager->getObject(
            DirectDebit::class,
            [
                'opsConfig' => $this->configMock
            ]
        );
    }

    public function testTemplate()
    {
        $this->assertEquals(DirectDebit::TEMPLATE, $this->block->getTemplate());
    }

    public function testDirectDebitCountryIds()
    {
        $this->markTestIncomplete('Mocking does not work properly here for some reason');
        $this->configMock->expects($this->once())
            ->method('getDirectDebitCountryIds')
            ->will($this->returnValue("AT, DE, NL"));


        $this->assertEquals(
            explode(',', 'AT, DE, NL'),
            $this->block->getDirectDebitCountryIds()
        );
    }
}
