<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/09/2015
 * Time: 13:56
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

use Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;
use Magento\Framework\Controller\ResultFactory;

class Save extends Rule
{

    protected $_type;

    protected $_params;


    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /*
            * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
        */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $params = $this->getRequest()->getParams();
        if (!isset($params['type']) || !$params['type']) {
            return;
        }

        $this->_type   = $params['type'];
        $this->_params = $params;
        /*
            * we use rule object for convenience
        */

        $conditionsSerializedString = $this->getConditionToSave();

        if ($data = $this->getRequest()->getPostValue()) {
            $ruleId = $this->getRequest()->getParam('id');

            $data['website_id'] = serialize($data['website_id']);

            $data['customer_group_id'] = serialize($data['customer_group_id']);
            $email_chain               = array();
            if (isset($data['email'])) {
                $emails = $data['email'];
                foreach ($emails as $email) {
                    if (isset($email['template']) && $email['template']) {
                        $template = $email['template'];

                        $day  = $email['day'];
                        $hour = $email['hour'];
                        $min  = $email['min'];

                        $email = array(
                                  'template' => $template,
                                  'day'      => $day,
                                  'hour'     => $hour,
                                  'min'      => $min,
                                 );

                        $email_chain[] = $email;
                    }
                }
            }//end if

            // process the attached files
            $attachedData = $this->getRequest()->getParam('img');

            $refinedAttachedData = [];

            if (is_array($attachedData) && !is_empty($attachedData)) {
                foreach ($attachedData as $attach) {
                    $refinedAttachedData[] = $attach;
                }
            }

            $data['attached_files'] = $refinedAttachedData;
            try {
                $data['email_chain'] = serialize($email_chain);

                $data['conditions_serialized'] = $conditionsSerializedString;

                $model = $this->ruleFactory->create()->load($ruleId)->setData($data);

                $model->save();

                $this->messageManager->addSuccess(__('You saved the follow up email rule.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving this field mapping.'));
            }
        }//end if

        $resultRedirect->setPath('abandonedcartreminder/rule/index');
        return $resultRedirect;

    }//end execute()


    public function getConditionToSave()
    {
        $type = $this->_type;

        $generalType = $this->heper->getAbandonedCartReminderTriggerGroup($type);
        switch ($generalType) {
            case 'cart':
                $condition = $this->getOrderCondition();
                break;

            case 'customer':
                $condition = $this->getCustomerCondition();
                break;

            default:
                $condition = '';
        }

        return $condition;

    }//end getConditionToSave()


    protected function getOrderCondition()
    {
        /*
            * @var  $catalogRule  \Magento\CatalogRule\Model\Rule
            */
        // $catalogRule =  $this->_objectManager->create('\Magento\CatalogRule\Model\Rule');
        $catalogRule = $this->_objectManager->create('\Magento\SalesRule\Model\Rule');

        $catalogRule->loadPost($this->_params['rule']);

        $asArray                    = $catalogRule->getConditions()->asArray();
        $conditionsSerializedString = serialize($asArray);
        return $conditionsSerializedString;

    }//end getOrderCondition()


    protected function getCustomerCondition()
    {
        $condition                  = $this->_params['condition'];
        $conditionsSerializedString = serialize($condition);
        return $conditionsSerializedString;

    }//end getCustomerCondition()

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::rule');

    }//end _isAllowed()
}//end class
