<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 05/11/2015
 * Time: 14:30
 */

namespace Magenest\AbandonedCartReminder\Model\System\Config\Source\Mail;

class Status
{
    const STATUS_QUEUED    = 0;
    const STATUS_SENT      = 2;
    const STATUS_FAILED    = 3;
    const STATUS_CANCELLED = 4;


    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
                self::STATUS_QUEUED    => __('queued'),
                self::STATUS_SENT      => __('sent'),
                self::STATUS_FAILED    => __('failed'),
                self::STATUS_CANCELLED => __('cancelled'),
               ];

    }//end getOptionArray()


    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = [
                         'value' => $index,
                         'label' => $value,
                        ];
        }

        return $result;

    }//end getAllOptions()


    /**
     * Retrieve option text by option value
     *
     * @param  string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;

    }//end getOptionText()


    /*
        * option active
     */


    public static function getOptionArrayActive()
    {
        return [
                self::IS_ACTIVE   => __('Active'),
                self::IS_INACTIVE => __('Inactive'),
               ];

    }//end getOptionArrayActive()


    public function getAllOptionactive()
    {
        $result = [];

        foreach (self::getOptionArrayActive() as $index => $value) {
            $result[] = [
                         'value' => $index,
                         'label' => $value,
                        ];
        }

        return $result;

    }//end getAllOptionactive()


    public function getOptionTextActive($optionId)
    {
        $options = self::getOptionArrayActive();

        return isset($options[$optionId]) ? $options[$optionId] : null;

    }//end getOptionTextActive()
}//end class
