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

class PaymentTerms implements \Magento\Framework\Data\OptionSourceInterface
{
    const TWO_TERMS = '2';
    const THREE_TERMS = '3';
    const FOUR_TERMS = '4';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TWO_TERMS,
                'label' => __('%1', self::TWO_TERMS),
            ],
            [
                'value' => self::THREE_TERMS,
                'label' => __('%1', self::THREE_TERMS),
            ],
            [
                'value' => self::FOUR_TERMS,
                'label' => __('%1', self::FOUR_TERMS),
            ],
        ];
    }
}
