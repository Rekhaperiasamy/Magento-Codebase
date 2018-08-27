<?php

namespace Netresearch\OPS\Test\Unit\Block;

class PlaceformShaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    private $_helper;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $configMock;

    /**
     * @var string
     */
    private $_shaKey;

    public function setUp()
    {
        parent::setUp();
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->configMock = $this->getMock('Netresearch\OPS\Model\Config', [], [], '', false, false);
        $this->configMock->expects($this->any())->method('getConfigData')->with('secret_key_type')->will($this->returnValue('sha1'));
        $this->_helper = $objectManager->getObject('Netresearch\OPS\Helper\Payment', ['oPSConfig' => $this->configMock]);
        $this->_shaKey = 'qu4rkkuchen12345';
    }

    public function testShaGenerationWithTestData()
    {
        $params = [
            'AMOUNT'                        => '2129',
            'CIVILITY'                      => 'Herr',
            'CURRENCY'                      => 'EUR',
            'ECOM_BILLTO_POSTAL_NAME_FIRST' => 'John',
            'ECOM_BILLTO_POSTAL_NAME_LAST'  => 'Doe',
            'ECOM_SHIPTO_DOB'               => '09/10/1940',
            'EMAIL'                         => 'john@doe.com',
            'ITEMID1'                       => 'article1',
            'ITEMNAME1'                     => 'coffee',
            'ITEMPRICE1'                    => '3.00',
            'ITEMQUANT1'                    => '4',
            'ITEMVAT1'                      => '0.57',
            'LANGUAGE'                      => 'de_DE',
            'ORDERID'                       => 'order123',
            'ORDERSHIPCOST'                 => '100',
            'ORDERSHIPTAX'                  => '6',
            'OWNERADDRESS'                  => 'test street',
            'OWNERCTY'                      => 'DE',
            'OWNERTELNO'                    => '+49 111 222 33 444',
            'OWNERTOWN'                     => 'Berlin',
            'OWNERZIP'                      => '10000',
            'PM'                            => 'Open Invoice DE',
            'PSPID'                         => 'NRMAGbillpay1',
        ];
        $expected = '695103f8891dfc80ea46369203925b898a381334';
        $shaSign = $this->_helper->getSHASign($params, $this->_shaKey, 0);
        $result   = $this->_helper->shaCrypt($shaSign);
        $this->assertEquals($expected, $result);
    }

    public function testShaGenerationWithSpecialChars()
    {
        $params = [
            'PSPID'                         => 'NRMAGbillpay1',
            'AMOUNT'                        => '560',
            'ORDERID'                       => 'TBI72',
            'CURRENCY'                      => 'EUR',
            'OWNERCTY'                      => 'DE',
            'ITEMVAT1'                      => '0.10',
            'LANGUAGE'                      => 'de_DE',
            'PM'                            => 'Open Invoice DE',
            'CIVILITY'                      => 'Herr',
            'EMAIL'                         => 'thomas.kappel@netresearch.de',
            'ORDERSHIPCOST'                 => '500',
            'ORDERSHIPTAX'                  => '0',
            'ECOM_BILLTO_POSTAL_NAME_FIRST' => 'Karla',
            'ECOM_BILLTO_POSTAL_NAME_LAST'  => 'Kolumna',
            'OWNERTELNO'                    => '64065460',
            'ITEMPRICE1'                    => '0.60',
            'ECOM_SHIPTO_DOB'               => '09/10/1940',
            'OWNERADDRESS'                  => 'Tierparkallee 2',
            'OWNERTOWN'                     => 'Leipzig',
            'ITEMID1'                       => '26',
            'ITEMNAME1'                     => 'Club Mate',
            'ITEMQUANT1'                    => '1',
            'OWNERZIP'                      => '04229',
        ];
        $expected = 'baf6099446e3bf93ecf26e622032e7db2139839c';
        $shaSign = $this->_helper->getSHASign($params, $this->_shaKey, 0);
        $result   = $this->_helper->shaCrypt($shaSign);
        $this->assertEquals($expected, $result);
    }
}
