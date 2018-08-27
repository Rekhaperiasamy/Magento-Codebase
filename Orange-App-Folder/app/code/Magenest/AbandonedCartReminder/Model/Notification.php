<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 10/06/2016
 * Time: 09:08
 */

namespace Magenest\AbandonedCartReminder\Model;

class Notification extends \Magento\Framework\Model\AbstractModel
{


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\AbandonedCartReminder\Model\Resource\Notification $resource,
        \Magenest\AbandonedCartReminder\Model\Resource\Notification\Collection $resourceCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;

        $this->_storeManager = $storeManager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }//end __construct()


    /**
     * Wrapper method for resource collection
     *
     * @param $customerId
     */
    public function getNotificationByCustomer($customerId)
    {
        return $this->getResourceCollection()->getNotificationByCustomer($customerId);

    }//end getNotificationByCustomer()


    /**
     * get the first paragraph customer can see
     */
    public function getFirstPart()
    {
        $content = $this->getContent();

        if ($content) {
            $message = $content['content'];
            return substr($message, 0, 50);
        }

    }//end getFirstPart()


    public function getSecondPart()
    {
        $content = $this->getContent();
        if ($content) {
            $message = $content['content'];
            return substr($message, 50);
        }

    }//end getSecondPart()


    public function getContent()
    {
        if ($this->getId()) {
            return $this->getResource()->getContent($this->getId());
        }

    }//end getContent()


    public function getTime()
    {

    }//end getTime()
}//end class
