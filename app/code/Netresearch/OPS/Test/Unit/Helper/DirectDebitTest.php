<?php

namespace Netresearch\OPS\Test\Unit\Helper;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class DirectDebitTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @return \Netresearch\OPS\Helper\DirectDebit
     */
    protected function getDirectDebitHelper()
    {
        return $this->objectManager->getObject('Netresearch\OPS\Helper\DirectDebit');
    }

    public function testGetDataHelper()
    {
        $this->assertTrue(
            $this->getDirectDebitHelper()->getDataHelper() instanceof \Netresearch\OPS\Helper\Data
        );
    }

    public function testGetQuoteHelper()
    {
        $this->assertTrue(
            $this->getDirectDebitHelper()->getQuoteHelper() instanceof \Netresearch\OPS\Helper\Quote
        );
    }

    public function testGetOrderHelper()
    {
        $this->assertTrue(
            $this->getDirectDebitHelper()->getOrderHelper() instanceof \Netresearch\OPS\Helper\Order
        );
    }

    public function testGetValidator()
    {
        $this->markTestIncomplete();
        $validator = $this->getMock('\Netresearch\OPS\Model\Validator\Payment\DirectDebit', [], [], '', false, false);
        $validatorFactory
            = $this->getMock('\Netresearch\OPS\Model\Validator\Payment\DirectDebitFactory', [], [], '', false, false);
        $validatorFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($validator));
        $helper = $this->objectManager->getObject(
            'Netresearch\OPS\Helper\DirectDebit',
            ['oPSValidatorPaymentDirectDebitFactory' => $validatorFactory]
        );

        $this->assertTrue(
            $helper->getValidator() instanceof \Netresearch\OPS\Model\Validator\Payment\DirectDebit
        );
    }

    public function testGetCountry()
    {
        $this->markTestIncomplete();
        $helper = $this->getDirectDebitHelper();
        $params = [];
        $this->assertEquals('', $helper->getCountry($params));
        $params['country'] = 'de';
        $this->assertEquals('DE', $helper->getCountry($params));
    }

    public function testHasIban()
    {
        $this->markTestIncomplete();
        $helper = $this->getDirectDebitHelper();
        $params = [];
        $this->assertFalse($helper->hasIban($params));
        $params['iban'] = '';
        $this->assertFalse($helper->hasIban($params));
        $params['iban'] = ' ';
        $this->assertFalse($helper->hasIban($params));
        $params['iban'] = '123456789';
        $this->assertTrue($helper->hasIban($params));
    }

    public function testSetDirectDebitDataToPayment()
    {
        $this->markTestIncomplete();
        $payment = $this->objectManager->getObject('\Magento\Quote\Model\Quote\Payment');
        $helper  = $this->getDirectDebitHelper();
        $params  = ['country' => 'de', 'account' => '', 'bankcode' => ''];
        $helper->setDirectDebitDataToPayment($payment, $params);
        $this->assertEquals(
            'Direct Debits DE',
            $payment->getAdditionalInformation('PM')
        );

        $params = [
            'country'  => 'de', 'CN' => 'Account Holder', 'account' => '',
            'bankcode' => ''
        ];
        $helper->setDirectDebitDataToPayment($payment, $params);
        $this->assertEquals(
            'Account Holder',
            $payment->getAdditionalInformation('CN')
        );

        $params = [
            'country' => 'nl', 'CN' => 'Account Holder',
            'account' => '1234567', 'bankcode' => ''
        ];
        $helper->setDirectDebitDataToPayment($payment, $params);
        $this->assertEquals(
            '1234567',
            $payment->getAdditionalInformation('CARDNO')
        );

        $params = [
            'country' => 'at', 'CN' => 'Account Holder',
            'account' => '1234567', 'bankcode' => '1234567'
        ];
        $helper->setDirectDebitDataToPayment($payment, $params);
        $this->assertEquals(
            '1234567BLZ1234567',
            $payment->getAdditionalInformation('CARDNO')
        );

        $params = [
            'country' => 'de', 'CN' => 'Account Holder',
            'account' => '1234567', 'bankcode' => '1234567'
        ];
        $helper->setDirectDebitDataToPayment($payment, $params);
        $this->assertEquals(
            '1234567BLZ1234567',
            $payment->getAdditionalInformation('CARDNO')
        );

        $params = [
            'country'  => 'de', 'CN' => 'Account Holder',
            'iban'     => 'DE1234567890', 'account' => '1234567',
            'bankcode' => '1234567'
        ];
        $helper->setDirectDebitDataToPayment($payment, $params);
        $this->assertEquals(
            'DE1234567890',
            $payment->getAdditionalInformation('CARDNO')
        );

        $params = [
            'country'  => 'at', 'CN' => 'Account Holder',
            'iban'     => 'DE1234567890', 'account' => '1234567',
            'bankcode' => '1234567'
        ];
        $helper->setDirectDebitDataToPayment($payment, $params);
        $this->assertEquals(
            '1234567BLZ1234567',
            $payment->getAdditionalInformation('CARDNO')
        );

        $params = [
            'country' => 'nl', 'CN' => 'Account Holder',
            'iban'    => 'NL1234567890', 'bic' => '12345678',
            'account' => '1234567', 'bankcode' => '1234567'
        ];
        $helper->setDirectDebitDataToPayment($payment, $params);
        $this->assertEquals(
            'NL1234567890',
            $payment->getAdditionalInformation('CARDNO')
        );
        $this->assertEquals(
            '12345678',
            $payment->getAdditionalInformation('BIC')
        );
    }
}
