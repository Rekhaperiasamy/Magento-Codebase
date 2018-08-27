<?php

namespace Netresearch\OPS\Test\Unit\Helper;

use Netresearch\OPS\Model\Payment\DirectDebit;
use Zend\Filter\Dir;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class QuoteTest extends \PHPUnit_Framework_TestCase
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

    public function testCleanUpOldPaymentInformation()
    {
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false, false);
        $quote->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $quoteFactory = $this->getMock('\Magento\Quote\Model\QuoteFactory', [], [], '', false, false);
        $quoteFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($quote));
        $quotePayment = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false, false);
        $quotePayment->expects($this->atLeastOnce())
            ->method('getAdditionalInformation')
            ->will($this->returnValueMap([['cvc', 123]]));
        $quotePayment->expects($this->once())
            ->method('setQuote')
            ->with($quote)
            ->will($this->returnSelf());
        $quotePaymentCollection = $this->getMock('\Magento\Quote\Model\ResourceModel\Quote\Payment\Collection', ['addFieldToFilter', 'setOrder', 'setPageSize', 'load', 'getIterator'], [], '', false, false);
        $quotePaymentCollection->expects($this->atLeastOnce())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $quotePaymentCollection->expects($this->once())
                               ->method('setOrder')
                               ->will($this->returnSelf());
        $quotePaymentCollection->expects($this->once())
                               ->method('setPageSize')
                               ->will($this->returnSelf());
        $quotePaymentCollection->expects($this->atLeastOnce())
                               ->method('getIterator')
                               ->will($this->returnValue(new \ArrayIterator([$quotePayment])));
        $quotePaymentCollectionFactory = $this->getMock('\Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory', [], [], '', false, false);
        $quotePaymentCollectionFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($quotePaymentCollection));
        $aliasHelper = $this->getMock('Netresearch\OPS\Helper\Alias', [], [], '', false, false);


        /** @var \Netresearch\OPS\Helper\Quote $helper */
        $helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Quote',
            [
                                                      'quotePaymentCollectionFactory' => $quotePaymentCollectionFactory,
                                                      'oPSAliasHelper' => $aliasHelper,
                                                      'quoteQuoteFactory' => $quoteFactory
            ]
        );

        $helper->cleanUpOldPaymentInformation();
    }

    public function testGetQuoteCurrency()
    {
        $baseCurrency = 'EUR';
        $forcedCurrency = new \Magento\Framework\DataObject();
        $forcedCurrency->setCode('USD');
        $store = new \Magento\Framework\DataObject(['base_currency_code' => $baseCurrency]);
        $storeManager = $this->getMock('\Magento\Store\Model\StoreManager', [], [], '', false, false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));
        $quote = $this->getMock('\Magento\Quote\Model\Quote', ['getStoreId'], [], '', false, false);
        $quote->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue(1));
        /** @var \Netresearch\OPS\Helper\Quote $helper */
        $helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Quote',
            [
                                                      'storeManager' => $storeManager
            ]
        );
        $this->assertEquals(
            $baseCurrency,
            $helper->getQuoteCurrency($quote)
        );

        $quote->setForcedCurrency($forcedCurrency);

        $this->assertEquals(
            $forcedCurrency->getCode(),
            $helper->getQuoteCurrency($quote)
        );
    }

    public function testGetPaymentActionForAuthorize()
    {
        $payment = new \Magento\Framework\DataObject();
        $order = new \Magento\Framework\DataObject();
        $order->setPayment($payment);
        $config = new \Magento\Framework\DataObject();
        $config->setPaymentAction(\Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_ACTION);
        $configFactory = $this->getMock('Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($config));

        /** @var \Netresearch\OPS\Helper\Quote $helper */
        $helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Quote',
            [
                                                      'oPSConfigFactory' => $configFactory,
            ]
        );
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_ACTION,
            $helper->getPaymentAction($order)
        );

        $order->getPayment()->setMethod(DirectDebit::CODE);
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            $helper->getPaymentAction($order)
        );
    }

    public function testGetPaymentActionForAuthorizeCapture()
    {
        $payment = new \Magento\Framework\DataObject();
        $order = new \Magento\Framework\DataObject();
        $order->setPayment($payment);
        $config = new \Magento\Framework\DataObject();
        $config->setPaymentAction('authorize_capture');
        $configFactory = $this->getMock('Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())
                      ->method('create')
                      ->will($this->returnValue($config));

        /** @var \Netresearch\OPS\Helper\Quote $helper */
        $helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Quote',
            [
                                                      'oPSConfigFactory' => $configFactory,
            ]
        );

        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            $helper->getPaymentAction($order)
        );
        $order->getPayment()->setMethod(DirectDebit::CODE);
        $this->assertEquals(
            \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            $helper->getPaymentAction($order)
        );
    }

    public function testGetQuoteWithAdminSession()
    {
        $quote = new \Magento\Framework\DataObject();
        $appState = $this->getMock('\Magento\Framework\App\State', [], [], '', false, false);
        $appState->expects($this->once())
            ->method('getAreaCode')
            ->will($this->returnValue(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE));
        $backendSessionQuote = $this->getMock('\Magento\Backend\Model\Session\Quote', [], [], '', false, false);
        $backendSessionQuote->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        /** @var \Netresearch\OPS\Helper\Quote $helper */
        $helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Quote',
            [
                                                      'appState' => $appState,
                                                      'backendSessionQuote' => $backendSessionQuote
            ]
        );



        $this->assertEquals($quote, $helper->getQuote());
    }

    public function testGetQuoteWithCheckoutSession()
    {
        $quote = new \Magento\Framework\DataObject();
        $appState = $this->getMock('\Magento\Framework\App\State', [], [], '', false, false);
        $appState->expects($this->once())
                 ->method('getAreaCode')
                 ->will($this->returnValue(false));
        $checkoutSession = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false, false);
        $checkoutSession->expects($this->any())
                            ->method('getQuote')
                            ->will($this->returnValue($quote));

        /** @var \Netresearch\OPS\Helper\Quote $helper */
        $helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Quote',
            [
                                                      'appState' => $appState,
                                                      'checkoutSession' => $checkoutSession
            ]
        );
        $this->assertEquals($quote, $helper->getQuote());
    }
}
