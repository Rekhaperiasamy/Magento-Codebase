<?php

namespace Netresearch\OPS\Test\Unit\Block;

class FormTest extends \PHPUnit_Framework_TestCase
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

    public function testIsUserRegistering()
    {
        $dataHelperMock = $this->getMock('Netresearch\OPS\Helper\Data', ['checkIfUserIsRegistering'], [], '', false, false);
        $dataHelperMock->expects($this->any())
            ->method('checkIfUserIsRegistering')
            ->will($this->returnValue(false));

        $block = $this->objectManager->getObject('Netresearch\OPS\Block\Form', ['oPSHelper' => $dataHelperMock]);
        $this->assertFalse($block->isUserRegistering());
    }

    public function testIsUserNotRegistering()
    {
        $dataHelperMock = $this->getMock('Netresearch\OPS\Helper\Data', ['checkIfUserIsNotRegistering'], [], '', false, false);
        $dataHelperMock->expects($this->any())
            ->method('checkIfUserIsNotRegistering')
            ->will($this->returnValue(false));

        $block = $this->objectManager->getObject('Netresearch\OPS\Block\Form', ['oPSHelper' => $dataHelperMock]);
        $this->assertFalse($block->isUserNotRegistering());
    }


    public function testGetPmLogo()
    {
        $block = $this->objectManager->getObject('Netresearch\OPS\Block\Form');
        $this->assertEquals(null, $block->getPmLogo());
    }

    public function testGetFrontendValidatorsAreEmtpyWhenNoExtraParamtersAreSubmitted()
    {
        /** @var \Magento\Quote\Model\Quote $quoteMock */
        $quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false, false);
        $quoteMock->setStoreId(0);
        $sessionMock = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false, false);
        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        /** @var \Netresearch\OPS\Model\Config $configMock */
        $configMock = $this->getMock('Netresearch\OPS\Model\Config', ['canSubmitExtraParameter'], [], '', false, false);
        $configMock->expects($this->once())
            ->method('canSubmitExtraParameter')
            ->will($this->returnValue(false));

        /** @var \Magento\Framework\Json\Encoder $jsonEncoder */
        $jsonEncoder = $this->objectManager->getObject('Magento\Framework\Json\Encoder');

        /** @var \Netresearch\OPS\Block\Form $block */
        $block = $this->objectManager->getObject(
            'Netresearch\OPS\Block\Form',
            [
                'checkoutSession' => $sessionMock,
                'oPSConfig' => $configMock,
                'jsonEncoder' => $jsonEncoder
            ]
        );

        $this->assertEquals($jsonEncoder->encode([]), $block->getFrontendValidators());
    }

    public function testGetFrontendValidatorsAreEmptyDueToEmptyValidators()
    {
        /** @var \Magento\Quote\Model\Quote $quoteMock */
        $quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false, false);
        $quoteMock->setStoreId(0);
        $sessionMock = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false, false);
        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        /** @var \Netresearch\OPS\Model\Config $configMock */
        $configMock = $this->getMock('Netresearch\OPS\Model\Config', [], [], '', false, false);
        $configMock->expects($this->once())
            ->method('canSubmitExtraParameter')
            ->will($this->returnValue(true));
        $configMock->expects($this->once())
            ->method('getParameterLengths')
            ->will($this->returnValue([]));
        $configMock->expects($this->once())
            ->method('getFrontendFieldMapping')
            ->will($this->returnValue([]));

        /** @var \Magento\Framework\Json\Encoder $jsonEncoder */
        $jsonEncoder = $this->objectManager->getObject('Magento\Framework\Json\Encoder');

        /** @var \Netresearch\OPS\Block\Form $block */
        $block = $this->objectManager->getObject(
            'Netresearch\OPS\Block\Form',
            [
                'checkoutSession' => $sessionMock,
                'oPSConfig' => $configMock,
                'jsonEncoder' => $jsonEncoder
            ]
        );

        $this->assertEquals($jsonEncoder->encode([]), $block->getFrontendValidators());
    }

    public function testGetFrontendValidatorsAreEmptyDueToUnmappedValidators()
    {
        /** @var \Magento\Quote\Model\Quote $quoteMock */
        $quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false, false);
        $quoteMock->setStoreId(0);
        $sessionMock = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false, false);
        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $configMock = $this->getMock('Netresearch\OPS\Model\Config', [], [], '', false, false);
        $configMock->expects($this->once())
            ->method('canSubmitExtraParameter')
            ->will($this->returnValue(true));
        $configMock->expects($this->once())
            ->method('getParameterLengths')
            ->will($this->returnValue(['Foo' => 50]));
        $configMock->expects($this->once())
            ->method('getFrontendFieldMapping')
            ->will($this->returnValue([]));

        /** @var \Magento\Framework\Json\Encoder $jsonEncoder */
        $jsonEncoder = $this->objectManager->getObject('Magento\Framework\Json\Encoder');

        /** @var \Netresearch\OPS\Block\Form $block */
        $block = $this->objectManager->getObject(
            'Netresearch\OPS\Block\Form',
            [
                'checkoutSession' => $sessionMock,
                'oPSConfig' => $configMock,
                'jsonEncoder' => $jsonEncoder
            ]
        );

        $this->assertEquals($jsonEncoder->encode([]), $block->getFrontendValidators());
    }


    public function testGetFrontendValidatorsAreNotEmpty()
    {
        /** @var \Magento\Quote\Model\Quote $quoteMock */
        $quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false, false);
        $quoteMock->setStoreId(0);
        $sessionMock = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false, false);
        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $configValues = ['CN' => 50, 'ECOM_BILLTO_POSTAL_POSTALCODE' => 10, 'ECOM_SHIPTO_POSTAL_POSTALCODE' => 10];

        $configMock = $this->getMock('Netresearch\OPS\Model\Config', [], [], '', false, false);
        $configMock->expects($this->once())
            ->method('canSubmitExtraParameter')
            ->will($this->returnValue(true));
        $configMock->expects($this->once())
            ->method('getParameterLengths')
            ->will($this->returnValue($configValues));
        $configMock->expects($this->once())
                   ->method('getFrontendFieldMapping')
                   ->will($this->returnValue(['CN'                            => [
                                                   'firstname' => 'billing:firstname',
                                                   'lastname'  => 'billing:lastname'
                                              ],
                                              'ECOM_SHIPTO_POSTAL_POSTALCODE' => 'shipping:postcode',
                                              'ECOM_BILLTO_POSTAL_POSTALCODE' => 'billing:postcode'
                                             ]));
        $helper = $this->objectManager->getObject('Netresearch\OPS\Helper\Data');

        /** @var \Magento\Framework\Json\Encoder $jsonEncoder */
        $jsonEncoder = $this->objectManager->getObject('Magento\Framework\Json\Encoder');

        /** @var \Netresearch\OPS\Block\Form $block */
        $block = $this->objectManager->getObject(
            'Netresearch\OPS\Block\Form',
            [
                'checkoutSession' => $sessionMock,
                'oPSConfig'       => $configMock,
                'oPSHelper'       => $helper,
                'jsonEncoder'     => $jsonEncoder
            ]
        );

        $this->assertEquals(
            $jsonEncoder->encode([
                                     'billing:firstname' => 50,
                                     'billing:lastname'  => 50,
                                     'billing:postcode'  => 10,
                                     'shipping:postcode' => 10
                                 ]),
            $block->getFrontendValidators()
        );
    }
}
