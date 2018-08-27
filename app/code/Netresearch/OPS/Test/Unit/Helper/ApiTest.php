<?php

namespace Netresearch\OPS\Test\Unit\Helper;

/**
 * Created by JetBrains PhpStorm.
 * User: michael
 * Date: 07.05.13
 * Time: 16:55
 * To change this template use File | Settings | File Templates.
 */

use \Netresearch\OPS\Model\Status\Feedback;

class ApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }

    public function testGetRedirectRouteFromStatus()
    {
        /** @var \Netresearch\OPS\Model\Config $configModel */
        $configModel = $this->getMock('Netresearch\OPS\Model\Config', [], [], '', false, false);
        $configModel->expects($this->atLeastOnce())
            ->method('getAcceptRedirectRoute')
            ->will($this->returnValue('accept'));
        $configModel->expects($this->atLeastOnce())
            ->method('getCancelRedirectRoute')
            ->will($this->returnValue('cancel'));
        $configModel->expects($this->atLeastOnce())
            ->method('getDeclineRedirectRoute')
            ->will($this->returnValue('decline'));
        $configModel->expects($this->atLeastOnce())
            ->method('getExceptionRedirectRoute')
            ->will($this->returnValue('exception'));

        $configFactory = $this->getMock('\Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($configModel));

        /** @var \Netresearch\OPS\Helper\Api $helper */
        $helper = $this->objectManager->getObject('Netresearch\OPS\Helper\Api', ['oPSConfigFactory' => $configFactory]);

        $this->assertEquals('accept', $helper->getRedirectRouteFromStatus(Feedback::OPS_ORDER_FEEDBACK_STATUS_ACCEPT));

        $this->assertEquals('cancel', $helper->getRedirectRouteFromStatus(Feedback::OPS_ORDER_FEEDBACK_STATUS_CANCEL));

        $this->assertEquals('decline', $helper->getRedirectRouteFromStatus(Feedback::OPS_ORDER_FEEDBACK_STATUS_DECLINE));

        $this->assertEquals('exception', $helper->getRedirectRouteFromStatus(Feedback::OPS_ORDER_FEEDBACK_STATUS_EXCEPTION));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage invalid status provided
     */
    public function testGetRedirectRouteFromWhenStatusIsNotInvalid()
    {
        $this->objectManager->getObject('Netresearch\OPS\Helper\Api')->getRedirectRouteFromStatus('');
    }
}
