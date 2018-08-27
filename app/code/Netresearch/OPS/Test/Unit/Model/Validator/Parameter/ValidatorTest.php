<?php
namespace Netresearch\OPS\Test\Unit\Model\Validator\Parameter;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\Validator
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\Validator\Parameter\Validator');
    }

    public function testIsValid()
    {
        $this->assertTrue($this->model->isValid(null));
        $this->model->addValidator(new \Zend_Validate_Alnum());
        $this->assertFalse($this->model->isValid(null));
        $this->assertTrue(0 < count($this->model->getMessages()));
    }

    public function testMultipleValidators()
    {
        $this->model->addValidator(new \Zend_Validate_Alnum());
        $this->model->addValidator(new \Zend_Validate_EmailAddress());
        $this->assertFalse($this->model->isValid(null));
        $this->assertTrue(1 < count($this->model->getMessages()));
    }
}
