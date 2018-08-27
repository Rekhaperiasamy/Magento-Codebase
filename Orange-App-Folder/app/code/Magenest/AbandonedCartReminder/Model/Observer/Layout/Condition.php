<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 17/11/2015
 * Time: 15:27
 */

namespace Magenest\AbandonedCartReminder\Model\Observer\Layout;

use Magento\Customer\Model\Logger;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Condition implements ObserverInterface
{

    /**
     * Logger of customer's log data.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * helper
     *
     * @var Logger
     */
    protected $heper;


    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger,
        \Magenest\AbandonedCartReminder\Helper\Data $data
    ) {
        $this->logger = $logger;
        $this->heper  = $data;

    }//end __construct()


    /**
     * Handler for 'customer_logout' event.
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*
            * @var $request \Magento\Framework\App\Request\Http\Proxy
        */
        $request  = $this->heper->getRequest();
        $pathInfo = $request->getPathInfo();
        $params   = $request->getParams();

        if ((strpos($pathInfo, 'abandonedcartreminder') !== false) && isset($params['type'])) {
            $type         = $params['type'];
            $triggerGroup = $this->classifyTrigger($type);

            if ($triggerGroup == 'cart') {
                $observer->getEvent()->getLayout()->getUpdate()->addHandle('abandonedcartreminder_rule_new_ab');
            } elseif ($triggerGroup == 'customer') {
                $observer->getEvent()->getLayout()->getUpdate()->addHandle('abandonedcartreminder_rule_new_birthday');
            }
        }

    }//end execute()


    /**
     * determine what is the group of trigger then use the result to update they layout
     * there are three taxomnomy
     * cart
     * catalog
     * customer
     *
     * @param  string $url
     * @return mixed
     */
    public function classifyTrigger($type)
    {
        $group = $this->heper->getAbandonedCartReminderTriggerGroup($type);
        return $group;

    }//end classifyTrigger()
}//end class
