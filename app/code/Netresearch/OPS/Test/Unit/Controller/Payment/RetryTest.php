<?php
/**
 * Test for Netresearch\OPS\Controller\Payment\Retry
 */

namespace Netresearch\OPS\Controller\Payment;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * Class RetryTest
 *
 * @package Netresearch\OPS\Controller\Payment
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class RetryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Netresearch\OPS\Helper\Order | \PHPUnit_Framework_MockObject_MockObject
     */
    private $oPSOrderHelper;

    /**
     * @var \Magento\Sales\Model\Order | \PHPUnit_Framework_MockObject_MockObject
     */
    private $order;

    /**
     * @var \Magento\Quote\Model\Quote | \PHPUnit_Framework_MockObject_MockObject
     */
    private $quote;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManager;

    /**
     * @var \Netresearch\OPS\Model\Config | \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $redirect;

    /**
     * @var \Netresearch\OPS\Helper\Payment | \PHPUnit_Framework_MockObject_MockObject
     */
    private $oPSPaymentHelper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteRepository;

    /**
     * @var \Magento\Customer\Model\Session | \PHPUnit_Framework_MockObject_MockObject
     */
    private $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session | \PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPage;

    /**
     * @var \Netresearch\OPS\Controller\Payment\Retry
     */
    private $model;

    public function setUp()
    {
        $this->objectManager    = new ObjectManager($this);
        $objectManagerMock      = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', [], [], '', false);
        $this->oPSOrderHelper   = $this->getMock('Netresearch\OPS\Helper\Order', [], [], '', false, false);
        $this->order            = $this->getMock('Magento\Sales\Model\Order', [], [], '', false, false);
        $this->request          = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->response         = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);
        $this->messageManager   = $this->getMock('Magento\Framework\Message\ManagerInterface', [], [], '', false);
        $this->redirect         = $this->getMock('Magento\Framework\App\Response\RedirectInterface', [], [], '', false);
        $this->oPSPaymentHelper = $this->getMock('Netresearch\OPS\Helper\Payment', [], [], '', false, false);
        $this->quoteRepository  = $this->getMock('Magento\Quote\Api\CartRepositoryInterface', [], [], '', false, false);
        $this->quote            = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false, false);
        $this->customerSession  = $this->getMock('Magento\Customer\Model\Session', [], [], '', false, false);
        $this->checkoutSession  = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $this->resultPage       = $this->getMock('Magento\Framework\View\Result\Page', [], [], '', false);
        $resultPageFactory      = $this->getMock('Magento\Framework\View\Result\PageFactory', [], [], '', false);
        $resultPageFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->resultPage);
        $context = $this->getMock('Magento\Framework\App\Action\Context', [], [], '', false);
        $context->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManagerMock);
        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $context->expects($this->once())
            ->method('getResponse')
            ->willReturn($this->response);
        $context->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManager);
        $context->expects($this->any())
            ->method('getRedirect')
            ->willReturn($this->redirect);

        $this->config  = $this->getMock('Netresearch\OPS\Model\Config', [], [], '', false, false);
        $configFactory = $this->getMock('Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())->method('create')->will($this->returnValue($this->config));

        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Controller\Payment\Retry',
            [
                'context'          => $context,
                'oPSOrderHelper'   => $this->oPSOrderHelper,
                'oPSPaymentHelper' => $this->oPSPaymentHelper,
                'oPSConfigFactory' => $configFactory,
                'customerSession'  => $this->customerSession,
                'checkoutSession'  => $this->checkoutSession,
                'quoteRepository'  => $this->quoteRepository,
                'pageFactory'      => $resultPageFactory
            ]
        );
    }

    public function testValidateOpsDataIsFalse()
    {
        $this->oPSOrderHelper
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($this->order));

        $this->order
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $this->messageManager->expects($this->once())
            ->method('addNoticeMessage')
            ->with(__('Hash not valid'))
            ->willReturn(true);

        $this->model->execute();
    }

    /**
     * @test
     */
    public function testCanRetryPaymentIsFalse()
    {
        $this->oPSOrderHelper
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($this->order));

        $this->order
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(123));

        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $payment->expects($this->any())
            ->method('getAdditionalInformation')
            ->will($this->returnValue(['status' => 909]));

        $this->order
            ->expects($this->once())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $this->order
            ->expects($this->once())
            ->method('getIncrementId')
            ->will($this->returnValue(123));

        $this->quoteRepository
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->quote));

        $this->oPSPaymentHelper
            ->expects($this->any())
            ->method('shaCryptValidation')
            ->willReturn(true);


        $this->oPSOrderHelper
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($this->order));

        $this->model->execute();
    }

    /**
     * @test
     */
    public function testCanRetryPaymentIsTrue()
    {
        $this->oPSOrderHelper
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($this->order));

        $this->order
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(123));

        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $payment->expects($this->any())
            ->method('getAdditionalInformation')
            ->will($this->returnValue(['status' => 59]));

        $this->order
            ->expects($this->once())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $this->order
            ->expects($this->once())
            ->method('getIncrementId')
            ->will($this->returnValue(123));

        $this->quoteRepository
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->quote));

        $this->oPSPaymentHelper
            ->expects($this->any())
            ->method('shaCryptValidation')
            ->willReturn(true);


        $this->oPSOrderHelper
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($this->order));

        $this->model->execute();
    }

    /**
     * @test
     */
    public function testCanRetryPayment()
    {
        $this->oPSOrderHelper
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($this->order));

        $this->order
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(123));

        $payment = $this->getMock('\Magento\Sales\Model\Order\Payment', [], [], '', false, false);
        $payment->expects($this->any())
            ->method('getAdditionalInformation')
            ->will($this->returnValue('foo'));

        $this->order
            ->expects($this->once())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $this->order
            ->expects($this->once())
            ->method('getIncrementId')
            ->will($this->returnValue(123));

        $this->quoteRepository
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->quote));

        $this->oPSPaymentHelper
            ->expects($this->any())
            ->method('shaCryptValidation')
            ->willReturn(true);


        $this->oPSOrderHelper
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($this->order));

        $this->model->execute();
    }
}
