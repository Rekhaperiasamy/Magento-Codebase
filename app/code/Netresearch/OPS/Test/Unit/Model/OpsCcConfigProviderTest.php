<?php

namespace Netresearch\OPS\Test\Unit\Model;

class OpsCcConfigProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\OpsCcConfigProvider
     */
    private $object;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolver;

    public function setUp()
    {
        parent::setUp();

        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->config = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $this->localeResolver = $this->getMock('\Magento\Framework\Locale\Resolver', [], [], '', false, false);
        $this->object = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\OpsCcConfigProvider',
            [
                'config'          => $this->config,
                'checkoutSession' => $this->checkoutSession,
                'localeResolver'  => $this->localeResolver
            ]
        );
    }

    public function testGetConfig()
    {
        $ccBrands = 'VISA,MC';
        $inlinePaymentCcTypes = 'MC';
        $hashUrl = 'hashUrl';
        $orderId = 123;
        $pspid = 'pspid';
        $aliasAcceptUrl = 'aliasAcceptUrl';
        $aliasExceptionUrl = 'aliasExceptionUrl';
        $url = 'url';
        $aliasManager = true;
        $locale = 'en-US';
        $isThreeDSecureEnabled = true;
        $cardConfig = [
            'ccBrands'              => explode(',', $ccBrands),
            'inlinePaymentCcTypes'  => $inlinePaymentCcTypes,
            'aliasAcceptUrl'        => $aliasAcceptUrl,
            'aliasManager'          => $aliasManager,
            'isThreeDSecureEnabled' => $isThreeDSecureEnabled,
            'brandImage'            => ['Visa' => null],
            'paymentMethod'         => 'CreditCard',
            'htpTemplate'           => null
        ];
        $directDebitConfig = $cardConfig;
        $directDebitConfig['paymentMethod'] = '';
        $htpConfig = [
            'hashUrl'           => $hashUrl,
            'orderId'           => $orderId,
            'pspid'             => $pspid,
            'aliasExceptionUrl' => $aliasExceptionUrl,
            'url'               => $url,
            'locale'            => $locale,
        ];
        $result['payment']['opsCc'] = $cardConfig;
        $result['payment']['opsDc'] = $cardConfig;
        $result['payment']['opsDirectDebit'] = $directDebitConfig;
        $result['payment']['opsHTP'] = $htpConfig;

        $this->config->expects($this->exactly(3))->method('getAcceptedCcTypes')->will($this->returnValue($ccBrands));
        $this->config->expects($this->exactly(3))->method('getInlinePaymentCcTypes')->will(
            $this->returnValue($inlinePaymentCcTypes)
        );
        $this->config->expects($this->once())->method('getGenerateHashUrl')->will($this->returnValue($hashUrl));
        $this->config->expects($this->once())->method('getPSPID')->will($this->returnValue($pspid));
        $this->config->expects($this->exactly(3))->method('getAliasAcceptUrl')->will(
            $this->returnValue($aliasAcceptUrl)
        );
        $this->config->expects($this->once())->method('getAliasExceptionUrl')->will(
            $this->returnValue($aliasExceptionUrl)
        );
        $this->config->expects($this->once())->method('getAliasGatewayUrl')->will($this->returnValue($url));
        $this->config->expects($this->exactly(3))->method('isAliasManagerEnabled')->will(
            $this->returnValue($aliasManager)
        );
        $this->config->expects($this->exactly(3))->method('get3dSecureIsActive')->will(
            $this->returnValue($isThreeDSecureEnabled)
        );
        $this->config->expects($this->exactly(3))->method('getCardTypes')->will($this->returnValue(['Visa']));
        $this->localeResolver->expects($this->once())->method('getLocale')->will($this->returnValue($locale));
        $this->checkoutSession->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue(new \Magento\Framework\DataObject(['id' => $orderId])));

        $this->assertEquals($result, $this->object->getConfig());
    }
}
