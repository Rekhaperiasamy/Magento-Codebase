<?php
namespace Netresearch\OPS\Test\Unit\Helper\Validation;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Validation\Result
     */
    private $helper;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    private $messages;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->messages      = ['foo' => 'bar'];
        $this->config        = $this->getMock('\Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->config->expects($this->any())->method('getFrontendFieldMapping')->will($this->returnValue($this->messages));
    }

    public function testGetValidationFailedResultWithFieldMapping()
    {
        $this->config->expects($this->once())
                     ->method('getFrontendFieldMapping')
                     ->will($this->returnValue($this->messages));
        $dataHelper = $this->getMock('Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $dataHelper->expects($this->any())
                   ->method('getFrontendValidationFields')
                   ->will($this->returnValue(['bar' => 'bar']));
        $this->helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Validation\Result',
            ['oPSHelper' => $dataHelper]
        );
        $this->helper->setConfig($this->config);
        $quote = new \Magento\Framework\DataObject();
        $result = $this->helper->getValidationFailedResult($this->messages, $quote);
        $this->assertArrayHasKey('fields', $result);
        $this->assertArrayHasKey('bar', $result['fields']);
    }

    public function testGetValidationFailedResultWithExistingAddress()
    {
        $dataHelper = $this->getMock('Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $dataHelper->expects($this->any())
                   ->method('getFrontendValidationFields')
                   ->will($this->returnValue(['bar' => 'bar']));
        $checkoutStepHelper = $this->getMock('Netresearch\OPS\Helper\Validation\Checkout\Step');
        $checkoutStepHelper->expects($this->exactly(2))->method('getStep')->will($this->onConsecutiveCalls(
            'billing',
            'shipping'
        ));
        $this->helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Validation\Result',
            [
                                                            'oPSHelper'                       => $dataHelper,
                                                            'oPSValidationCheckoutStepHelper' => $checkoutStepHelper
                                                        ]
        );
        $this->helper->setConfig($this->config);
        $quote = new \Magento\Framework\DataObject([
                                                       'shipping_address' => new \Magento\Framework\DataObject(['id' => 1]),
                                                       'billing_address'  => new \Magento\Framework\DataObject(['id' => 1])
                                                   ]);
        $result = $this->helper->getValidationFailedResult($this->messages, $quote);
        $this->assertArrayHasKey('fields', $result);
        $this->assertArrayHasKey('billing-address-select', $result['fields']);
        $result = $this->helper->getValidationFailedResult($this->messages, $quote);
        $this->assertArrayHasKey('fields', $result);
        $this->assertArrayHasKey('shipping-address-select', $result['fields']);
    }

    public function testCleanResult()
    {
        $quote = new \Magento\Framework\DataObject();
        $prevResult = ['update_section' => 'foo'];
        $dataHelper = $this->getMock('Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $dataHelper->expects($this->any())
                   ->method('getFrontendValidationFields')
                   ->will($this->returnValue(['bar' => 'bar']));
        $checkoutStepHelper = $this->getMock('Netresearch\OPS\Helper\Validation\Checkout\Step');
        $checkoutStepHelper->expects($this->any())->method('getStep')->will($this->returnValue(''));
        $this->helper = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Validation\Result',
            [
                                                            'oPSHelper'                       => $dataHelper,
                                                            'oPSValidationCheckoutStepHelper' => $checkoutStepHelper
                                                        ]
        );
        $this->helper->setConfig($this->config);
        $this->helper->setResult($prevResult);
        $result = $this->helper->getValidationFailedResult($this->messages, $quote);
        $this->assertArrayNotHasKey('update_section', $result);
    }
}
