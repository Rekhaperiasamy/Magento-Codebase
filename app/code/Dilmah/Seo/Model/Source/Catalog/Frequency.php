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
 * This is used to get the frequency options.
 */
class Frequency extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * frequency never.
     */
    const FREQUENCY_NEVER = 1;

    /**
     * frequency yearly.
     */
    const FREQUENCY_YEARLY = 2;

    /**
     * frequency monthly.
     */
    const FREQUENCY_MONTHLY = 3;

    /**
     * frequency weekly.
     */
    const FREQUENCY_WEEKLY = 4;

    /**
     * frequency daily.
     */
    const FREQUENCY_DAILY = 5;

    /**
     * frequency hourly.
     */
    const FREQUENCY_HOURLY = 6;

    /**
     * frequency always.
     */
    const FREQUENCY_ALWAYS = 7;

    /**
     * Get all options.
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                [
                    'value' => self::FREQUENCY_NEVER,
                    'label' => __('Never'),
                ],
                [
                    'value' => self::FREQUENCY_YEARLY,
                    'label' => __('Yearly'),
                ],
                [
                    'value' => self::FREQUENCY_MONTHLY,
                    'label' => __('Monthly'),
                ],
                [
                    'value' => self::FREQUENCY_WEEKLY,
                    'label' => __('Weekly'),
                ],
                [
                    'value' => self::FREQUENCY_DAILY,
                    'label' => __('Daily'),
                ],
                [
                    'value' => self::FREQUENCY_HOURLY,
                    'label' => __('Hourly'),
                ],
                [
                    'value' => self::FREQUENCY_ALWAYS,
                    'label' => __('Always'),
                ],
            ];
        }

        return $this->_options;
    }
}
