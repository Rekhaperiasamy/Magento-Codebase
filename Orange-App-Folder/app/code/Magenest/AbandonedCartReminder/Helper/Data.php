<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 19/11/2015
 * Time: 16:37
 */
namespace Magenest\AbandonedCartReminder\Helper;

use Psr\Log\LoggerInterface as Logger;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_AB_PERIOD = 'abandonedcartreminder/abandoned_cart/period';

    const XML_PATH_CONFIG_MOBILE_ENABLE = 'abandonedcartreminder/mobile/enable';

    const XML_PATH_CONFIG_MOBILE_REQUIRED = 'abandonedcartreminder/mobile/is_required';

    protected $_type;

    protected $scopeConfig;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();

    }//end __construct()


    /**
     * determine whether admin allow mobile input field in customer register form
     *
     * @return boolean
     */
    public function getIsEnableMobileInput()
    {
        $enable = $this->scopeConfig->getValue(self::XML_PATH_CONFIG_MOBILE_ENABLE);

        if ($enable == '1') {
            return true;
        } else {
            return false;
        }

    }//end getIsEnableMobileInput()


    /**
     * determine whether mobile is required field
     *
     * @return boolean
     */
    public function getIsMobileInputRequire()
    {
        $required = $this->scopeConfig->getValue(self::XML_PATH_CONFIG_MOBILE_REQUIRED);

        if ($required == '1') {
            return true;
        } else {
            return false;
        }

    }//end getIsMobileInputRequire()


    public function getAbandonedCartPeriod()
    {
        $timePeriod = $this->scopeConfig->getValue(self::XML_PATH_AB_PERIOD);
        return $timePeriod;

    }//end getAbandonedCartPeriod()


    public function getRequest()
    {
        return $this->_request;

    }//end getRequest()


    public function getAbandonedCartReminderTriggerGroup($trigger)
    {
        switch ($trigger) {
            case 'abandoned_cart':
            case 'order_is_placed':
            case 'order_status_pending_payment':
            case 'order_status_processing':
            case 'order_status_closed':
            case 'order_status_complete':
            case 'order_status_canceled':
            case 'order_status_holded':
            case 'order_status_payment_review':
                return 'cart';

                break;
            case 'customer_registration':
            case 'customer_registration_no_purchase':
            case 'customer_birthday':
            case 'newsletter_subscribe':
            case 'newsletter_unsubscribe':
            case 'wishlist_shared':
            case 'wishlist_is_abandoned':
                return 'customer';

                break;
        }//end switch

    }//end getAbandonedCartReminderTriggerGroup()


    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->_type;

    }//end getType()


    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->_type = $type;

    }//end setType()


    public function getCancelTrigger()
    {
        $cancel_trigger = array(
                           [
                            'label'         => __('Newsletter'),
                            'optgroup-name' => __('Newsletter'),
                            'value'         => [
                                                [
                                                 'value' => 'newsletter_subcribe',
                                                 'label' => __('Customer un-subscribe newsletter '),
                                                ],

                                               ],
                           ],

                          );

        if ($this->_type) {
            switch ($this->_type) {
                case 'order_status_new':
                case 'customer_status_complete':
                    $cancel_trigger[0]['value'][] = [
                                                     'value' => 'customer_status_complete',
                                                     'label' => __('Order obtained  status Complete'),
                                                    ];
                    break;
            }
        }

        return $cancel_trigger;

    }//end getCancelTrigger()
}//end class
