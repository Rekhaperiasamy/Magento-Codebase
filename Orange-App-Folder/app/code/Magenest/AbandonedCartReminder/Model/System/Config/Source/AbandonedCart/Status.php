<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 13/11/2015
 * Time: 10:35
 */
namespace Magenest\AbandonedCartReminder\Model\System\Config\Source\AbandonedCart;

class Status
{

    // the cart is new, it has not pass the abandoned time (1 hour by default)
    const  STATUS_NEW = 0;

    // the cart is abandoned
    const  STATUS_ABANDONED = 1;

    // the cart i recovered after abandoned
    const  STATUS_RECOVERED = 2;

    // the cart is converted to order
    const  STATUS_CONVERTED = 3;


    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
                self::STATUS_NEW       => __('new'),
                self::STATUS_ABANDONED => __('abandoned'),
                self::STATUS_RECOVERED => __('recovered'),
                self::STATUS_CONVERTED => __('converted'),
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
}//end class
