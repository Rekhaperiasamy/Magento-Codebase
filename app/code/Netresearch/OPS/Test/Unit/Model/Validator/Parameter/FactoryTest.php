<?php
namespace Netresearch\OPS\Test\Unit\Model\Validator\Parameter;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\Factory
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\Validator
     */
    private $validator;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\Length
     */
    private $lengthValidator;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->validator     = $this->getMock(
            '\Netresearch\OPS\Model\Validator\Parameter\Validator',
            null,
            [],
            '',
            false,
            false
        );
        $validatorFactory    = $this->getMock(
            '\Netresearch\OPS\Model\Validator\Parameter\ValidatorFactory',
            [],
            [],
            '',
            false,
            false
        );
        $validatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->validator));
        $this->lengthValidator  = $this->getMock(
            '\Netresearch\OPS\Model\Validator\Parameter\Length',
            null,
            [],
            '',
            false,
            false
        );
        $lengthValidatorFactory = $this->getMock(
            '\Netresearch\OPS\Model\Validator\Parameter\LengthFactory',
            [],
            [],
            '',
            false,
            false
        );
        $lengthValidatorFactory->expects($this->any())->method('create')->will($this->returnValue($this->lengthValidator));
        $this->config  = new \Magento\Framework\DataObject(['all_cc_types' => ['VISA']]);
        $configFactory = $this->getMock('Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())->method('create')->will($this->returnValue($this->config));
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Validator\Parameter\Factory',
            [
                                                           'oPSValidatorParameterValidatorFactory' => $validatorFactory,
                                                           'oPSValidatorParameterLengthFactory'    => $lengthValidatorFactory,
                                                           'oPSConfigFactory'                      => $configFactory
                                                       ]
        );
    }

    public function testGetValidatorFor()
    {
        $this->config->setData('parameter_lengths', ['foo' => 30]);
        $validator = $this->model->getValidatorFor(null);
        $this->assertSame($this->validator, $validator);
        $this->assertEquals(0, count($validator->getValidators()));
        $validator
            = $this->model->getValidatorFor(\Netresearch\OPS\Model\Validator\Parameter\Factory::TYPE_REQUEST_PARAMS_VALIDATION);
        $this->assertSame($this->validator, $validator);
        $this->assertEquals(1, count($validator->getValidators()));
        $this->assertSame($this->lengthValidator, current($validator->getValidators()));
        $this->assertTrue(0 < count(current($validator->getValidators())->getFieldLengths()));
    }
}
