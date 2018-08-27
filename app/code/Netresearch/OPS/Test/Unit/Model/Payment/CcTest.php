<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment;

class CcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\Cc
     */
    private $model;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Info
     */
    private $paymentInfo;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote\Payment
     */
    private $payment;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config
     */
    private $scopeConfig;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager   = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentInfo     = $this->getMock('\Magento\Sales\Model\Order\Payment\Info', [], [], '', false, false);
        $this->checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->payment         = $this->getMock('\Magento\Quote\Model\Quote\Payment', ['save'], [], '', false, false);
        $this->config          = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->config->expects($this->any())->method('getInlinePaymentCcTypes')->will($this->returnValue(explode(
            ',',
            'American Express,Diners Club,Maestro,MaestroUK,MasterCard,VISA,JCB'
        )));
        $this->scopeConfig = $this->getMock('\Magento\Framework\App\Config', [], [], '', false, false);
        $this->model       = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Payment\Cc',
            [
                                                                 'checkoutSession' => $this->checkoutSession,
                                                                 'oPSConfig'       => $this->config,
                                                                 'scopeConfig'     => $this->scopeConfig
                                                             ]
        );
        $this->model->setInfoInstance($this->paymentInfo);
    }

    public function testBrand()
    {
        $this->payment->setAdditionalInformation('CC_BRAND', 'VISA');
        $this->assertEquals('VISA', $this->model->getOpsBrand($this->payment), 'VISA should have brand VISA');
        $this->assertEquals('CreditCard', $this->model->getOpsCode($this->payment), 'VISA should be a CreditCard');
        $this->assertTrue(
            $this->model->hasBrandAliasInterfaceSupport($this->payment),
            'VISA should support alias interface'
        );
        $this->payment->setAdditionalInformation('CC_BRAND', 'UNEUROCOM');
        $this->assertEquals(
            'UNEUROCOM',
            $this->model->getOpsBrand($this->payment),
            'UNEUROCOM should have brand UNEUROCOM'
        );
        $this->assertEquals(
            'UNEUROCOM',
            $this->model->getOpsCode($this->payment),
            'UNEUROCOM should have code UNEUROCOM'
        );
        $this->assertFalse(
            $this->model->hasBrandAliasInterfaceSupport($this->payment),
            'UNEUROCOM should NOT support alias interface'
        );
        $this->payment->setAdditionalInformation('CC_BRAND', 'PostFinance card');
        $this->assertEquals(
            'PostFinance card',
            $this->model->getOpsBrand($this->payment),
            'PostFinance Card should have brand "PostFinance card"'
        );
        $this->assertEquals(
            'PostFinance Card',
            $this->model->getOpsCode($this->payment),
            'PostFinance Card should have code "PostFinance Card"'
        );
        $this->assertFalse(
            $this->model->hasBrandAliasInterfaceSupport($this->payment),
            'PostFinance Card should NOT support alias interface'
        );
        $this->payment->setAdditionalInformation('CC_BRAND', 'PRIVILEGE');
        $this->assertEquals(
            'PRIVILEGE',
            $this->model->getOpsBrand($this->payment),
            'PRIVILEGE should have brand PRIVILEGE'
        );
        $this->assertEquals('CreditCard', $this->model->getOpsCode($this->payment), 'PRIVILEGE should be a CreditCard');
        $this->assertFalse(
            $this->model->hasBrandAliasInterfaceSupport($this->payment),
            'PRIVILEGE should NOT support alias interface'
        );
    }

    public function testOrderPlaceRedirectUrl()
    {
        $this->config->expects($this->any())->method('get3dSecureRedirectUrl')->will($this->returnValue('foo'));
        $this->config->expects($this->any())->method('getPaymentRedirectUrl')->will($this->returnValue('foo'));
        $this->payment->setAdditionalInformation('CC_BRAND', 'VISA');
        $this->assertFalse(
            $this->model->getOrderPlaceRedirectUrl($this->payment),
            'VISA should NOT require a redirect after checkout'
        );
        $this->payment->setAdditionalInformation('CC_BRAND', 'VISA');
        $this->payment->setAdditionalInformation('HTML_ANSWER', 'BASE64ENCODEDSTRING');
        $this->assertInternalType(
            'string',
            $this->model->getOrderPlaceRedirectUrl($this->payment),
            'If Brand is VISA and HTML_ANSWER isset, a redirect should happen after checkout'
        );
        $this->payment->setAdditionalInformation('CC_BRAND', 'PRIVILEGE');
        $this->assertInternalType(
            'string',
            $this->model->getOrderPlaceRedirectUrl($this->payment),
            'PRIVILEGE should require a redirect after checkout'
        );
    }

    public function testIsZeroAmountAuthorizationAllowed()
    {
        $this->config->expects($this->any())
                     ->method('getPaymentAction')
                     ->will($this->onConsecutiveCalls(
                         \Netresearch\OPS\Model\Payment\PaymentAbstract::ACTION_AUTHORIZE_CAPTURE,
                         \Netresearch\OPS\Model\Payment\PaymentAbstract::ACTION_AUTHORIZE_CAPTURE,
                         \Netresearch\OPS\Model\Payment\PaymentAbstract::ACTION_AUTHORIZE
                     ));
        $this->scopeConfig->expects($this->any())->method('getValue')->will($this->onConsecutiveCalls(0, 1, 0));
        $this->assertFalse($this->model->isZeroAmountAuthorizationAllowed());
        $this->assertFalse($this->model->isZeroAmountAuthorizationAllowed());
        $this->assertFalse($this->model->isZeroAmountAuthorizationAllowed());
    }

    public function testZeroAmountAuthAllowed()
    {
        $this->config->expects($this->once())
                     ->method('getPaymentAction')
                     ->will($this->returnValue(\Netresearch\OPS\Model\Payment\PaymentAbstract::ACTION_AUTHORIZE));
        $this->scopeConfig->expects($this->once())->method('getValue')->will($this->returnValue(1));
        $this->assertTrue($this->model->isZeroAmountAuthorizationAllowed());
    }

    public function testSetCanCapture()
    {
        $this->assertTrue($this->model->canCapture());
        $this->model->setCanCapture(false);
        $this->assertFalse($this->model->canCapture());
    }

    public function testGetOpsBrand()
    {
        $this->payment->setAdditionalInformation('CC_BRAND', 'VISA');
        $this->checkoutSession->expects($this->once())
                              ->method('getQuote')
                              ->will($this->returnValue(new \Magento\Framework\DataObject(['payment' => $this->payment])));
        $this->assertEquals('VISA', $this->model->getOpsBrand(null));
    }
}
