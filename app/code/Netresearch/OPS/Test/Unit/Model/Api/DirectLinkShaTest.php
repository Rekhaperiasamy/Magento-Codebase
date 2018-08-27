<?php

namespace Netresearch\OPS\Test\Unit\Model\Api;

class DirectLinkShaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Api\DirectLink
     */
    private $model;

    /**
     * @var string
     */
    private $shaKey;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $paymentHelper;



    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->paymentHelper = $this->getMock('\Netresearch\OPS\Helper\Payment', ['getCryptMethod'], [], '', false, false);
        $this->paymentHelper->expects($this->any())->method('getCryptMethod')->will($this->returnValue('sha1'));
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Api\DirectLink',
            ['oPSPaymentHelper' => $this->paymentHelper]
        );
        $this->shaKey = 'ksdf239sdnkvs2e9';
    }

    public function testShaGenerationWithoutSpecialChars()
    {
        $params = [
            'ALIAS' => 'foo'
        ];
        $expected = $params;
        $expected['SHASIGN'] = '44194456a31b8ea1de461612b19f7255732438d5';
        $this->assertEquals($expected, $this->model->getEncodedParametersWithHash($params, $this->shaKey, 0));
    }

    public function testShaGenerationWithSpecialChars()
    {
        $params = [
            'AMOUNT'    => '36980',
            'CARDNO'    => '257354109BLZ86010090',
            'CN'        => 'André Herrn',
            'CURRENCY'  => 'EUR',
            'ED'        => '9999',
            'OPERATION' => 'SAL',
            'ORDERID'   => '20190',
            'PM'        => 'Direct Debits DE',
            'PSPID'     => 'NRMAGENTO',
            'PSWD'      => 'magento1',
            'USERID'    => 'NRMAGENTO1API',
        ];
        $expected = $params;
        $expected['SHASIGN'] = 'eb95f7d66879e9801fdbdf75095ce23147202c30';
        $result = $this->model->getEncodedParametersWithHash($params, $this->shaKey, 0);
        $this->assertEquals($expected, $result);
    }
}
