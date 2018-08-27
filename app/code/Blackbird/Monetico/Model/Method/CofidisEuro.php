<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Model\Method;

/**
 * Class CofidisEuro
 *
 * @package Blackbird\Monetico\Model\Method
 */
class CofidisEuro extends \Blackbird\Monetico\Model\AbstractMethod
{
    const METHOD_CODE = 'monetico_1euro';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;
}
