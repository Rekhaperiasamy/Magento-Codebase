<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 09/06/2016
 * Time: 16:46
 */
namespace Magenest\AbandonedCartReminder\Block;

class Link extends \Magento\Framework\View\Element\Html\Link
{

    protected $_template = 'Magenest_AbandonedCartReminder::link.phtml';

    /**
     * @var  $notification  \Magenest\AbandonedCartReminder\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var  \Magento\Customer\Model\Session
     */
    protected $customerSession;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\AbandonedCartReminder\Model\NotificationFactory $notificationFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->notificationFactory = $notificationFactory;

        $this->customerSession = $customerSession;

    }//end __construct()


    /**
     * AbandonedCartReminder
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('giftregistry/guest/search');

    }//end getHref()


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Message Notification');

    }//end getLabel()


    public function getCloseUrl()
    {
        return $this->getUrl('abandonedcartreminder/notify/close');

    }//end getCloseUrl()


    /**
     * get the notification that user need to read
     *
     * @return integer
     */
    public function getNotificationCount()
    {
        $notifications = $this->getNotificationCollection();
        if (is_object($notifications)) {
            return $notifications->getSize();
        }

    }//end getNotificationCount()


    /**
     * Get notification by customer id
     *
     * @return mixed
     */
    public function getNotificationCollection()
    {
        $customerId = $this->customerSession->getCustomerId();

        if ($customerId) {
            $notificationModel = $this->notificationFactory->create();

            $notifications = $notificationModel->getNotificationByCustomer($customerId);
            return $notifications;
        }

    }//end getNotificationCollection()
}//end class
