<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 20/05/2016
 * Time: 10:59
 */
namespace Magenest\AbandonedCartReminder\Model\Config\Source;

class Account implements \Magento\Framework\Option\ArrayInterface
{

    protected $helper;

    protected $info;


    /**
     * @param \Magenest\AbandonedCartReminder\Helper\Connector $helper
     */
    public function __construct(\Magenest\AbandonedCartReminder\Helper\Connector $helper)
    {
        $this->helper = $helper;
        $this->info   = $this->helper->getUserInformation();

    }//end __construct()


    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (is_array($this->info)) {
            return [
                    [
                     'value' => 'User Name',
                     'label' => $this->info['username'],
                    ],
                    [
                     'value' => 'Reputation',
                     'label' => $this->info['reputation'],
                    ],
                    [
                     'value' => 'Hourly Quota',
                     'label' => $this->info['hourly_quota'],
                    ],
                    [
                     'value' => 'Backlog',
                     'label' => $this->info['backlog'],
                    ],
                   ];
        } else {
            return [
                    [
                     'value' => 'Error',
                     'label' => __('Your api key is not correct'),
                    ],
                   ];
        }//end if

    }//end toOptionArray()
}//end class
