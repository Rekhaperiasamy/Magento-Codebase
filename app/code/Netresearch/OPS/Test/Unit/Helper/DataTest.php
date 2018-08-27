<?php

namespace Netresearch\OPS\Test\Unit\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
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
     * @test
     */
    public function getModuleVersionString()
    {
        $moduleList = $this->getMockForAbstractClass('Magento\Framework\Module\ModuleListInterface');
        $moduleList->expects($this->once())
            ->method('getOne')
            ->with('Netresearch_OPS')
            ->will($this->returnValue(['setup_version' => '2.0.0']));

        /** @var \Netresearch\OPS\Helper\Data $helper */
        $helper = $this->objectManager->getObject('Netresearch\OPS\Helper\Data', ['moduleList' => $moduleList]);

        $this->assertSame('OGNM2200', $helper->getModuleVersionString());
    }
    
    public function testCheckIfUserIsRegistering()
    {
        $quote = new \Magento\Framework\DataObject();
        $checkout = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false, false);
        $checkout->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        /** @var \Netresearch\OPS\Helper\Data $helper */
        $helper = $this->objectManager->getObject('Netresearch\OPS\Helper\Data', ['checkoutSession' => $checkout]);

        $quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER);
        $this->assertTrue($helper->checkIfUserIsRegistering());
        
        $quote->setCheckoutMethod(\Magento\Quote\Model\Quote::CHECKOUT_METHOD_LOGIN_IN);
        $this->assertTrue($helper->checkIfUserIsRegistering());

        $quote->setCheckoutMethod('not_registering');
        $this->assertFalse($helper->checkIfUserIsRegistering());
    }

    public function testCheckIfUserIsNotRegistering()
    {
        $quote = new \Magento\Framework\DataObject();
        $checkout = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false, false);
        $checkout->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        /** @var \Netresearch\OPS\Helper\Data $helper */
        $helper = $this->objectManager->getObject('Netresearch\OPS\Helper\Data', ['checkoutSession' => $checkout]);

        $quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER);
        $this->assertTrue($helper->checkIfUserIsNotRegistering());

        $quote->setCheckoutMethod(\Magento\Quote\Model\Quote::CHECKOUT_METHOD_LOGIN_IN);
        $this->assertFalse($helper->checkIfUserIsNotRegistering());

        $quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_GUEST);
        $this->assertFalse($helper->checkIfUserIsNotRegistering());
    }

    public function testClearMsg()
    {
        /** @var \Netresearch\OPS\Helper\Data $helper */
        $helper = $this->objectManager->getObject('Netresearch\OPS\Helper\Data');
        $testArray = ['cvc' => '1', 'CVC' => '2', 'test' => 'me'];
        $testArray = $helper->clearMsg($testArray);
        $this->assertFalse(array_key_exists('cvc', $testArray));
        $this->assertFalse(array_key_exists('CVC', $testArray));
        $this->assertTrue(array_key_exists('test', $testArray));
        $testString = '{"CVC":"123"}';
        $this->assertFalse(strpos($helper->clearMsg($testString), 'CVC'));
        $testString = '{"CVC":"123","CN":"Some Name"}';
        $this->assertFalse(strpos($helper->clearMsg($testString), 'CVC'));
        $testString = '{"cvc":"123","CN":"Some Name"}';
        $this->assertFalse(strpos($helper->clearMsg($testString), 'cvc'));
        $this->assertTrue(false !== strpos($helper->clearMsg($testString), 'CN'));

        $testString = 'a:3:{s:5:"Alias";s:14:"10290855992990";s:3:"CVC";s:3:"777";s:2:"CN";s:13:"Homer Simpson";}';
        $this->assertFalse(strpos($helper->clearMsg($testString), 'CVC'));
        $this->assertTrue(false !== strpos($helper->clearMsg($testString), 'Homer'));
    }
}
