<?php
namespace Netresearch\OPS\Test\Unit\Model\Validator\Payment;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DirectDebitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Validator\Payment\DirectDebit
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Helper\DirectDebit
     */
    private $directDebitHelper;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $dataHelper;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->directDebitHelper = $this->getMock('\Netresearch\OPS\Helper\DirectDebit', null, [], '', false, false);
        $this->dataHelper = $this->getMock('\Netresearch\OPS\Helper\Data', null, [], '', false, false);
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Validator\Payment\DirectDebit',
            ['oPSDirectDebitHelper' => $this->directDebitHelper, 'oPSHelper' => $this->dataHelper]
        );
    }

    public function testIsValidWithInvalidDataReturnsFalse()
    {
        $this->markTestIncomplete();
        $directDebitData = [];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = ['CN' => 'foo'];
        $this->assertFalse($this->model->isValid($directDebitData));
        $this->assertTrue(0 < $this->model->getMessages());
        $directDebitData = ['CN' => 'foo', 'country' => 'de'];
        $this->assertFalse($this->model->isValid($directDebitData));
    }

    public function testIsValidWithBankAccountData()
    {
        $this->markTestIncomplete();
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'de',
            'account' => '12345678',
            'iban'    => ''
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'de',
            'account' => '12345678a',
            'iban'    => ''
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => '',
            'country' => 'de',
            'account' => '12345678a',
            'iban'    => ''
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'at',
            'account' => '12345678',
            'iban'    => ''
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'nl',
            'account' => '12345678',
            'iban'    => ''
        ];
        $this->assertTrue($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'       => 'foo',
            'country'  => 'de',
            'account'  => '12345678',
            'iban'     => '',
            'bankcode' => '12345678'
        ];
        $this->assertTrue($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'       => 'foo',
            'country'  => 'at',
            'account'  => '12345678',
            'iban'     => '',
            'bankcode' => '12345678'
        ];
        $this->assertTrue($this->model->isValid($directDebitData));
    }

    public function testIsValidWithIbanData()
    {
        $this->markTestIncomplete();
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'de',
            'account' => '',
            'iban'    => 'DE12345456677891234545667789'
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'at',
            'account' => '',
            'iban'    => 'AT12345456677891234545667789'
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'nl',
            'account' => '',
            'iban'    => 'NL12345456677891234545667789'
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'de',
            'account' => '',
            'iban'    => 'DE65160500003502221536'
        ];
        $this->assertTrue($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'de',
            'account' => '',
            'iban'    => 'DE65 1605 0000 3502 2215 36'
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'de',
            'account' => '',
            'iban'    => 'DE89370400440532013000'
        ];
        $this->assertTrue($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'nl',
            'account' => '',
            'iban'    => 'NL39RABO0300065264'
        ];
        $this->assertTrue($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'nl',
            'account' => '',
            'iban'    => 'NL39RABO0300065264',
            'bic'     => 'RABONL2U'
        ];
        $this->assertTrue($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'nl',
            'account' => '',
            'iban'    => 'NL39RABO0300065264',
            'bic'     => '012345678912'
        ];
        $this->assertFalse($this->model->isValid($directDebitData));
        $directDebitData = [
            'CN'      => 'foo',
            'country' => 'nl',
            'account' => '',
            'iban'    => 'NL39RABO0300065264',
            'bic'     => '01234567891'
        ];
        $this->assertTrue($this->model->isValid($directDebitData));
    }

    public function testSetdirectDebitHelper()
    {
        $this->markTestIncomplete();
        $this->model->setDirectDebitHelper($this->directDebitHelper);
        $this->assertEquals(
            get_class($this->directDebitHelper),
            get_class($this->model->getDirectDebitHelper())
        );
    }
}
