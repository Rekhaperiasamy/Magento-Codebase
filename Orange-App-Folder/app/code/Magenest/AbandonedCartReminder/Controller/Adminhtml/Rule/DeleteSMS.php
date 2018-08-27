<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 14/06/2016
 * Time: 21:43
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class DeleteSMS extends \Magento\Backend\App\Action
{

    /**
     * @var \Magenest\AbandonedCartReminder\Model\MessageFactory
     */
    protected $smsFactory;


    /**
     * @param \Magento\Backend\App\Action\Context                  $context
     * @param \Magenest\AbandonedCartReminder\Model\MessageFactory $messageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magenest\AbandonedCartReminder\Model\MessageFactory $messageFactory
    ) {
        parent::__construct($context);
        $this->smsFactory = $messageFactory;

    }//end __construct()


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $id           = $this->getRequest()->getParam('id');
        $responseData = [];

        try {
            $this->smsFactory->create()->load($id)->delete();
            $responseData['type']     = 'success';
            $responseData['messsage'] = __('You have removed sms successfully');
        } catch (\Exception $e) {
            $responseData['type']     = 'error';
            $responseData['messsage'] = $e->getMessage();
        }

        $resultJson->setData($responseData);
        return $resultJson;

    }//end execute()

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::rule');

    }//end _isAllowed()
}//end class
