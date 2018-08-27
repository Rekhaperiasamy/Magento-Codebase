<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Validator;

/**
 * Class Pool
 */
class Pool
{
    /**
     * @var array
     */
    protected $validators = [];

    /**
     * @param array $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    /**
     * Get Validators defined in di
     *
     * @param string $type
     * @return array
     */
    public function getValidators($type)
    {
        return isset($this->validators[$type]) ? $this->validators[$type] : [];
    }
}
