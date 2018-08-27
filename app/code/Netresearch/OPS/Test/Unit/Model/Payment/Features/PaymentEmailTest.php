<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment\Features;

/**
 * PaymentEmailTest.php
 *
 * @author    paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 */
class PaymentEmailTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\Features\PaymentEmail
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Helper\Order
     */
    private $orderHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $paymentHelper;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Url
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->orderHelper   = $this->getMock('\Netresearch\OPS\Helper\Order', [], [], '', false, false);
        $this->paymentHelper = $this->getMock(
            '\Netresearch\OPS\Helper\Payment',
            [],
            [],
            '',
            false,
            false
        );
        $this->config        = new \Magento\Framework\DataObject();
        $configFactory       = $this->getMock('Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())->method('create')->will($this->returnValue($this->config));
        $this->urlBuilder       = $this->getMock('\Magento\Framework\Url', [], [], '', false, false);
        $this->transportBuilder = $this->getMock(
            '\Magento\Framework\Mail\Template\TransportBuilder',
            [],
            [],
            '',
            false,
            false
        );
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\Features\PaymentEmail',
            [
                'oPSOrderHelper' => $this->orderHelper,
                'oPSConfigFactory' => $configFactory,
                'oPSPaymentHelper' => $this->paymentHelper,
                'urlBuilder' => $this->urlBuilder,
                'transportBuilder' => $this->transportBuilder
            ]
        );
    }

    public function testIsAvailableForOrder()
    {
        // given object is no order model -> returns false
        $order = new \Magento\Framework\DataObject();
        $this->assertFalse($this->model->isAvailableForOrder($order));
        // given payment has not fitting status -> returns false
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->setAdditionalInformation(['status' => 9]);
        $order = $this->getMock('\Magento\Sales\Model\Order', ['getPayment'], [], '', false, false);
        $order->expects($this->once())->method('getPayment')->will($this->returnValue($payment));
        $this->assertFalse($this->model->isAvailableForOrder($order));
        // payment has relevant status -> returns true
        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment->setAdditionalInformation(['status' => 1]);
        $order = $this->getMock('\Magento\Sales\Model\Order', ['getPayment'], [], '', false, false);
        $order->expects($this->once())->method('getPayment')->will($this->returnValue($payment));
        $this->assertTrue($this->model->isAvailableForOrder($order));
    }

    public function testResendPaymentInfo()
    {
        $emailTemplate = 'email_template';
        $paymentUrl    = 'url';
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment       = $this->getMock('\Magento\Sales\Model\Order\Payment', ['save'], [], '', false, false);
        $payment
            ->expects($this->any())
            ->method('save')
            ->will($this->returnValue(null));

        $payment->setMethod(\Netresearch\OPS\Model\Payment\Flex::CODE);
        $payment->setMethodInstance('foo');

        $order = $this->getMock('\Magento\Sales\Model\Order', ['save'], [], '', false, false);
        $order->setData('customer_email', 'a@bc.de')
              ->setData('customer_firstname', 'Hans')
              ->setData('id', 1)
              ->setData('customer_lastname', 'Wurst')
              ->setStoreId(0)
              ->setPayment($payment);

        $this->orderHelper->expects($this->once())
            ->method('getOpsOrderId')
            ->will($this->returnValue(1));

        $this->paymentHelper
            ->expects($this->once())
            ->method('getSHAInSet')
            ->will($this->returnValue('foo'));

        $this->paymentHelper
            ->expects($this->once())
            ->method('shaCrypt')
            ->will($this->returnValue('a87sdfg7a8s6d'));

        $this->config->setShaInCode('123123');
        $this->config->setResendPaymentInfoTemplate($emailTemplate);

        $transport = $this->getMock('Magento\Framework\Mail\Transport', [], [], '', false, false);
        $transport
            ->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue(true));

        $this->transportBuilder->expects($this->once())
                               ->method('setTemplateIdentifier')
                               ->with($emailTemplate)
                               ->will($this->returnValue($this->transportBuilder));

        $this->transportBuilder->expects($this->once())
            ->method('setTemplateOptions')
            ->with([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $order->getStoreId()
            ])
            ->will($this->returnValue($this->transportBuilder));
        $this->transportBuilder->expects($this->once())
                               ->method('setTemplateVars')
                               ->will($this->returnValue($this->transportBuilder));
        $this->transportBuilder->expects($this->once())
                               ->method('setFrom')
                               ->will($this->returnValue($this->transportBuilder));
        $this->transportBuilder
            ->expects($this->once())
            ->method('addTo')
            ->with(
                $order->getCustomerEmail(),
                $order->getCustomerName()
            )
            ->will($this->returnValue($this->transportBuilder));

        $this->transportBuilder
            ->expects($this->once())
            ->method('getTransport')
            ->will($this->returnValue($transport));

        $this->model->resendPaymentInfo($order);
    }
}
