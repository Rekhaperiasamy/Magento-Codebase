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
namespace Blackbird\Monetico\Model\Config\Source;

class Environment implements \Magento\Framework\Data\OptionSourceInterface
{
    const ENVIRONMENT_PRODUCTION = 'production';
    const ENVIRONMENT_SANDBOX = 'sandbox';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENVIRONMENT_SANDBOX,
                'label' => __('Sandbox'),
            ],
            [
                'value' => self::ENVIRONMENT_PRODUCTION,
                'label' => __('Production'),
            ],
        ];
    }
}
