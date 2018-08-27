<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model\Validator\Parameter;

class Factory
{
    const TYPE_REQUEST_PARAMS_VALIDATION = 'request_validation';

    protected $validator = null;

    protected $config = null;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\LengthFactory
     */
    protected $oPSValidatorParameterLengthFactory;

    public function __construct(
        \Netresearch\OPS\Model\Validator\Parameter\ValidatorFactory $oPSValidatorParameterValidatorFactory,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Model\Validator\Parameter\LengthFactory $oPSValidatorParameterLengthFactory
    ) {
        $this->oPSValidatorParameterLengthFactory = $oPSValidatorParameterLengthFactory;
        $this->validator = $oPSValidatorParameterValidatorFactory->create();
        $this->config = $oPSConfigFactory->create();
    }

    /**
     * creates validator for given type
     *
     * @param $type - the requested type
     *
     * @return \Netresearch\OPS\Model\Validator\Parameter\Validator
     */
    public function getValidatorFor($type)
    {
        if ($type == self::TYPE_REQUEST_PARAMS_VALIDATION) {
            $this->createRequestParamsValidator();
        }

        return $this->validator;
    }

    /**
     * configures the validator for validation of the request parameter
     *
     * @return $this
     */
    protected function createRequestParamsValidator()
    {
        $validator = $this->oPSValidatorParameterLengthFactory->create();
        $validator->setFieldLengths($this->config->getParameterLengths());
        $this->validator->addValidator($validator);

        return $this;
    }
}
