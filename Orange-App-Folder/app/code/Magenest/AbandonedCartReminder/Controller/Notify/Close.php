<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 13/06/2016
 * Time: 15:32
 */
namespace Magenest\AbandonedCartReminder\Controller\Notify;

use Magento\Framework\Controller\ResultFactory;

class Close extends \Magento\Framework\App\Action\Action
{

    /**
     * @var  \Magenest\AbandonedCartReminder\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session       $checkoutSession
     * @param \Magento\Customer\Model\Session       $customerSession
     * @param \Magento\Framework\UrlInterface       $urlInterface
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magenest\AbandonedCartReminder\Model\NotificationFactory $notificationFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->notificationFactory = $notificationFactory;
        $this->customerSession     = $customerSession;
        parent::__construct($context);

    }//end __construct()


    /**
     * Dispatch request
     * get the request of close notification with its id and changet it status
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $notification = $this->notificationFactory->create()->load($id);

        $responseContent = [];

        try {
            if ($notification->getId()) {
                $notification->addData(['is_read'=>1])->save();
                $responseContent['type']    = 'success';
                $responseContent['message'] = __('You have remove notification successfully');
            }
        } catch (\Exception $e) {
            $responseContent['type']    = 'error';
            $responseContent['message'] = $e->getMessage();
        }

        /*
            * @var \Magento\Framework\Controller\Result\Json $resultJson
        */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseContent);
        return $resultJson;

    }//end execute()
}//end class
