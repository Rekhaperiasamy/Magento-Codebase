<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */


namespace Amasty\Acart\Model;

class Date
{
    const SECONDS_IN_DAY = 86400;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->dateTime = $dateTime;
        $this->date = $date;
    }

    /**
     * @param $timestamp
     * @return null|string
     */
    public function getFormattedDate($timestamp)
    {
        return $this->dateTime->formatDate($timestamp);
    }

    /**
     * @return int
     */
    public function getCurrentTimestamp()
    {
        return $this->date->gmtTimestamp();
    }

    /**
     * @param int|string $days
     * @return int
     */
    public function convertDaysInSeconds($days)
    {
        return (int)$days * self::SECONDS_IN_DAY;
    }
}
