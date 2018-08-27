<?php
namespace Netresearch\OPS\Test\Unit\Model\Payment;

class OpenInvoiceNlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Payment\OpenInvoiceNl
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Payment\OpenInvoiceNl');
    }

    public function testQuestionRequired()
    {
        $order         = new \Magento\Framework\DataObject();
        $requestParams = [];
        $formFields    = [
            'OWNERADDRESS'                     => '',
            'ECOM_BILLTO_POSTAL_STREET_NUMBER' => '',
            'ECOM_SHIPTO_POSTAL_STREET_LINE1'  => '',
            'ECOM_SHIPTO_POSTAL_STREET_NUMBER' => ''
        ];
        /** @var \Netresearch\OPS\Model\Payment\OpenInvoiceNl $model */
        $model = $this->getMock(
            '\Netresearch\OPS\Model\Payment\OpenInvoiceNl',
            ['getFormFields'],
            [],
            '',
            false,
            false
        );
        $model->expects($this->any())->method('getFormFields')->will($this->returnValue($formFields));
        $this->assertTrue(
            $model->hasFormMissingParams($order, $requestParams, $formFields),
            'expected missing params'
        );
        $this->assertInstanceOf('\Magento\Framework\Phrase', $model->getQuestion($order, $requestParams));
        $this->assertEquals(
            [
                                'OWNERADDRESS',
                                'ECOM_BILLTO_POSTAL_STREET_NUMBER',
                                'ECOM_SHIPTO_POSTAL_STREET_LINE1',
                                'ECOM_SHIPTO_POSTAL_STREET_NUMBER'
                            ],
            $model->getQuestionedFormFields($order, $requestParams)
        );
    }

    public function testQuestionNotRequired()
    {
        $order         = new \Magento\Framework\DataObject();
        $requestParams = [
            'foo'                              => 'bar',
            'ECOM_BILLTO_POSTAL_STREET_NUMBER' => '14'
        ];
        $formFields    = [
            'OWNERADDRESS'                     => 'Nowhere',
            'ECOM_BILLTO_POSTAL_STREET_NUMBER' => '14',
            'ECOM_SHIPTO_POSTAL_STREET_LINE1'  => 'Somewhere',
            'ECOM_SHIPTO_POSTAL_STREET_NUMBER' => '1'
        ];
        /** @var \Netresearch\OPS\Model\Payment\OpenInvoiceNl $model */
        $model = $this->getMock(
            '\Netresearch\OPS\Model\Payment\OpenInvoiceNl',
            ['getFormFields'],
            [],
            '',
            false,
            false
        );
        $model->expects($this->any())->method('getFormFields')->will($this->returnValue($formFields));
        $this->assertFalse(
            $model->hasFormMissingParams($order, $requestParams, $formFields),
            'expected no missing params'
        );
        /* independent from that we expect to get question and questioned params when calling these methods directly */
        $this->assertInstanceOf('\Magento\Framework\Phrase', $model->getQuestion($order, $requestParams));
        $this->assertEquals(
            [
                                'OWNERADDRESS',
                                'ECOM_BILLTO_POSTAL_STREET_NUMBER',
                                'ECOM_SHIPTO_POSTAL_STREET_LINE1',
                                'ECOM_SHIPTO_POSTAL_STREET_NUMBER'
                            ],
            $model->getQuestionedFormFields($order, $requestParams)
        );
    }

    /**
     * assure that openInvoiceNL can capture partial
     */
    public function testCanCapturePartial()
    {
        $this->assertTrue($this->model->canCapturePartial());
    }
}
