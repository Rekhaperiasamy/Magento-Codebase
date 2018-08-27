<?php
/**
 * Netresearch_OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2017 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 *
 * TransactionalMailPluginTest.php
 *
 * @category  ops
 * @package   Netresearch_OPS
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 */

namespace Netresearch\OPS\Test\Unit\Plugin;

use Netresearch\OPS\Plugin\TransactionalMailPlugin;

class TransactionalMailPluginTest extends \PHPUnit_Framework_TestCase
{

    /** @var  TransactionalMailPlugin */
    private $plugin;
    /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */
    private $objectManager;
    private $flag = [];

    protected function setUp()
    {
        parent::setUp();
        $orderSenderMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Email\Sender\OrderSender::class)
            ->setMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderSenderMock->expects($this->atMost(1))
            ->method('send')
            ->will(
                $this->returnCallback([$this, 'setFlag'])
            );

        $invoiceSenderMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Email\Sender\InvoiceSender::class)
            ->setMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();
        $invoiceSenderMock->expects($this->atMost(1))
            ->method('send')
            ->will(
                $this->returnCallback([$this, 'setFlag'])
            );

        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->plugin = $this->objectManager->getObject(
            TransactionalMailPlugin::class,
            [
                'orderSender'   => $orderSenderMock,
                'invoiceSender' => $invoiceSenderMock
            ]
        );
    }

    public function testAfterProcessResponseWithoutFeedback()
    {
        $resultArgument = $this->getResultArgumentMock();
        $handler = $this->getHandlerMock();

        $result = $this->plugin->afterProcessResponse($handler, $resultArgument);

        $this->assertNull($result->getMethodInstance()->getInfoInstance()->getOrder()->getEmailSent());
        $this->assertNull($result->getMethodInstance()->getInfoInstance()->getCreatedInvoice()->getEmailSent());
    }

    public function testAfterProcessResponseWithFeedback()
    {
        $resultArgument = $this->getResultArgumentMock(true);
        $handler = $this->getHandlerMock();

        $result = $this->plugin->afterProcessResponse($handler, $resultArgument);

        $this->assertTrue($this->flag[0] instanceof \Magento\Sales\Model\Order);
        $this->assertTrue($this->flag[1] instanceof \Magento\Sales\Model\Order\Invoice);
    }

    /**
     * @return \Netresearch\OPS\Model\Response\Handler
     */
    private function getHandlerMock()
    {
        /** @var \Netresearch\OPS\Model\Response\Handler $handler */
        $handler = $this->getMockBuilder(\Netresearch\OPS\Model\Response\Handler::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $handler;
    }

    /**
     * @param bool $feedback
     *
     * @return \Netresearch\OPS\Model\Response\Type\Capture
     */
    private function getResultArgumentMock($feedback = false)
    {
        /** @var \Netresearch\OPS\Model\Response\Type\Capture $resultArgument */
        $resultArgument = $this->getMockBuilder(\Netresearch\OPS\Model\Response\Type\Capture::class)
            ->setMethods(['getShouldRegisterFeedback', 'getMethodInstance', 'getConfig'])
            ->disableOriginalConstructor()
            ->getMock();

        $configMock = $this->getMockBuilder(\Netresearch\OPS\Model\Config::class)
            ->setMethods(['getSendInvoice'])
            ->disableOriginalConstructor()
            ->getMock();

        $configMock->expects($this->atMost(1))
            ->method('getSendInvoice')
            ->will($this->returnValue(true));

        $resultArgument->expects($this->atMost(1))
            ->method('getConfig')
            ->will($this->returnValue($configMock));

        $invoiceMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Invoice::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderMock = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $methodMock = new \Magento\Framework\DataObject(
            [
                'info_instance' => new \Magento\Framework\DataObject(
                    [
                        'created_invoice' => $invoiceMock,
                        'order'           => $orderMock
                    ]
                )
            ]
        );

        $resultArgument->expects($this->any())
            ->method('getMethodInstance')
            ->will($this->returnValue($methodMock));

        $resultArgument->expects($this->once())
            ->method('getShouldRegisterFeedback')
            ->will($this->returnValue($feedback));

        return $resultArgument;
    }

    public function setFlag($value)
    {
        $this->flag[] = $value;
    }
}
