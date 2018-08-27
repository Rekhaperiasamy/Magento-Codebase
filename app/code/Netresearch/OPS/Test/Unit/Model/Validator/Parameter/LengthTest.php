<?php
namespace Netresearch\OPS\Test\Unit\Model\Validator\Parameter;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\Length
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Validator\Parameter\Length');
    }

    public function testValidationPassed()
    {
        $this->assertTrue($this->model->isValid(null));
        $this->assertTrue($this->model->isValid(new \Magento\Framework\DataObject()));
        $this->assertTrue($this->model->isValid([]));
        $map = ['foo' => 5, 'bar' => 4, 'baz' => 3, 'borg' => 5];
        $this->model->setFieldLengths($map);
        $data = ['foo' => '12345', 'bar' => '1234', 'baz' => '123', 'borg' => null];
        $this->assertTrue($this->model->isValid($data));
    }

    public function testValidationFailed()
    {
        $map = ['foo' => 5, 'bar' => 4, 'baz' => 3];
        $this->model->setFieldLengths($map);
        $data = ['foo' => '123456', 'bar' => '1234', 'baz' => '1238'];
        $this->assertFalse($this->model->isValid($data));
        $this->assertTrue(2 == count($this->model->getMessages()));
        $map = ['foo' => 5, 'bar' => 4, 'baz' => 3];
        $this->model->setFieldLengths($map);
        $data = ['foo' => '12345', 'bar' => '12345', 'baz' => '123'];
        $this->assertFalse($this->model->isValid($data));
        $this->assertTrue(3 == count($this->model->getMessages()));
    }
}
