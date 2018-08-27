<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Seo\Model\Source\Catalog;

/**
 * This is used to get the priority option.
 */
class Priority extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * priority first.
     */
    const PRIORITY_FIRST = 1;

    /**
     * priority second.
     */
    const PRIORITY_SECOND = 2;

    /**
     * priority third.
     */
    const PRIORITY_THIRD = 3;

    /**
     * priority fourth.
     */
    const PRIORITY_FOURTH = 4;

    /**
     * priority fifth.
     */
    const PRIORITY_FIFTH = 5;

    /**
     * priority sixth.
     */
    const PRIORITY_SIXTH = 6;

    /**
     * priority seventh.
     */
    const PRIORITY_SEVENTH = 7;

    /**
     * priority eighth.
     */
    const PRIORITY_EIGHTH = 8;

    /**
     * priority ninth.
     */
    const PRIORITY_NINTH = 9;

    /**
     * priority tenth.
     */
    const PRIORITY_TENTH = 10;

    /**
     * priority eleventh.
     */
    const PRIORITY_ELEVENTH = 11;

    /**
     * Returning all the options.
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                [
                    'value' => self::PRIORITY_FIRST,
                    'label' => __('0'),
                ],
                [
                    'value' => self::PRIORITY_SECOND,
                    'label' => __('1'),
                ],
                [
                    'value' => self::PRIORITY_THIRD,
                    'label' => __('0.1'),
                ],
                [
                    'value' => self::PRIORITY_FOURTH,
                    'label' => __('0.2'),
                ],
                [
                    'value' => self::PRIORITY_FIFTH,
                    'label' => __('0.3'),
                ],
                [
                    'value' => self::PRIORITY_SIXTH,
                    'label' => __('0.4'),
                ],
                [
                    'value' => self::PRIORITY_SEVENTH,
                    'label' => __('0.5'),
                ],
                [
                    'value' => self::PRIORITY_EIGHTH,
                    'label' => __('0.6'),
                ],
                [
                    'value' => self::PRIORITY_NINTH,
                    'label' => __('0.7'),
                ],
                [
                    'value' => self::PRIORITY_TENTH,
                    'label' => __('0.8'),
                ],
                [
                    'value' => self::PRIORITY_ELEVENTH,
                    'label' => __('0.9'),
                ],
            ];
        }

        return $this->_options;
    }
}
