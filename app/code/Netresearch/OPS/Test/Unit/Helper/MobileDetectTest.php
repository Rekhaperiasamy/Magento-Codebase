<?php

namespace Netresearch\OPS\Test\Unit\Helper;

class MobileDetectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /** @var \Netresearch\OPS\Helper\MobileDetect $helper */
    protected $helper;

    protected $detectorMock;

    public function setUp()
    {
        parent::setup();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->detectorMock = $this->getMock('\Detection\MobileDetect', [], [], '', false, false);
        $this->helper       = $this->objectManager->getObject('\Netresearch\OPS\Helper\MobileDetect');
        $this->helper->setDetector($this->detectorMock);
    }

    public function testGetDeviceTypeMobile()
    {
        $this->detectorMock
            ->expects($this->once())
            ->method('isMobile')
            ->willReturn(true);

        $this->detectorMock
            ->expects($this->once())
            ->method('isTablet')
            ->willReturn(false);

        $this->assertEquals(
            \Netresearch\OPS\Helper\MobileDetect::DEVICE_TYPE_MOBILE,
            $this->helper->getDeviceType()
        );
    }

    public function testGetDeviceTypeTablet()
    {
        $this->detectorMock
            ->expects($this->once())
            ->method('isMobile')
            ->willReturn(false);

        $this->detectorMock
            ->expects($this->once())
            ->method('isTablet')
            ->willReturn(true);

        $this->assertEquals(\Netresearch\OPS\Helper\MobileDetect::DEVICE_TYPE_TABLET, $this->helper->getDeviceType());
    }

    public function testGetDeviceTypeComputer()
    {
        $this->detectorMock
            ->expects($this->once())
            ->method('isMobile')
            ->willReturn(false);

        $this->detectorMock
            ->expects($this->once())
            ->method('isTablet')
            ->willReturn(false);

        $this->assertEquals(\Netresearch\OPS\Helper\MobileDetect::DEVICE_TYPE_COMPUTER, $this->helper->getDeviceType());
    }
}
