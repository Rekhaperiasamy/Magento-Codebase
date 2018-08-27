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

class Post extends Rule
{

    /**
     * @var  string
     */
    protected $_type;

    /**
     * @var  array
     */
    protected $_params;

    /**
     * @var  \Magenest\AbandonedCartReminder\Model\MessageFactory
     */
    protected $smsFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\AbandonedCartReminder\Model\RuleFactory $ruleFactory,
        \Magenest\AbandonedCartReminder\Helper\Data $helper,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magenest\AbandonedCartReminder\Model\MessageFactory $messageFactory
    ) {
        parent::__construct($context, $registry, $ruleFactory, $helper, $resultLayoutFactory);
        $this->smsFactory = $messageFactory;

    }//end __construct()


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

        $type = $this->getRequest()->getParam('type');
        if (!isset($params['type']) || !$params['type']) {
            return;
        }

        $this->_type   = $params['type'];
        $this->_params = $params;
        /*
            * we use rule object for convenience
        */

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

            // process the sms chain
            $smsIds=[];
            if (isset($data['sms']) && !empty($data['sms'])) {
                $smsIds = $this->processSMS($data['sms']);
            }

            // process the attached files
            $attachedData = $this->getRequest()->getParam('img');

            $refinedAttachedData = [];

            if (is_array($attachedData) && !empty($attachedData)) {
                foreach ($attachedData as $attach) {
                    $refinedAttachedData[] = $attach;
                }
            }

            $data['attached_files'] = serialize($refinedAttachedData);

            //fix me:
            //Aiden L
            $mediaAs = $this->getRequest()->getParam('product');

            if (isset ($mediaAs['media_gallery']['images'])) {

                $images = $mediaAs['media_gallery']['images'];

                //example of $img
                /**
                 * $img=[
                 * 'position' => 1 ,
                 * 'file' => '/m/s/ms6_1.png.tmp' ,
                 * 'value_id' => '',
                 * 'label' =>' ',
                 * 'disabled' =>' ',
                 * 'media_type' => 'image',
                 * 'removed' => ''
                 * ]
                 *
                 */
                $data['attached_files'] = serialize($images);
                $refineImages=[];

                foreach ($images as $key=>$img)
                {
                    if ($img['removed'] != '1')
                     $refineImages['images'][]=$img;
                }

                $data['attached_files'] = serialize($refineImages);
            }


            // if there are old attached_files value
            try {
                if (empty($email_chain)) {
                    throw  (new \Exception('Email chain is required fields'));
                }

                $conditionsSerializedString = $this->getConditionToSave();
                $data['email_chain']        = serialize($email_chain);

                $data['sms_chain'] = serialize($smsIds);

                $data['conditions_serialized'] = $conditionsSerializedString;

                // incase there is not order related event we check other condition
                if (isset($data['condition'])) {
                    if (is_array($data['condition'])) {
                        $data['conditions_serialized'] = serialize($data['condition']);
                    }
                }

                if (!$data['id']) {
                    unset($data['id']);
                }

                $model = $this->ruleFactory->create()->setData($data);

                $model->save();

                $this->messageManager->addSuccess(__('You saved the follow up email rule.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, $e->getMessage());
            }//end try
        }//end if

        $resultRedirect->setPath('abandonedcartreminder/rule/index');
        return $resultRedirect;

    }//end execute()


    /**
     * @param $smsData array
     * @return string
     */
    public function processSMS($smsData)
    {
        $smsOutPut = [];
        if (is_array($smsData) && !empty($smsData)) {
            foreach ($smsData as $sms) {
                $id = $sms['id'];
                // delete the deleted message
                if ($sms['is_deleted'] === '1') {
                    $sms = $this->smsFactory->create()->load($id);

                    if ($sms->getId()) {
                        $sms->delete();
                    }
                } else {
                    // edit the existing sms
                    if ($sms['is_new'] === '0') {
                        $sms = $this->smsFactory->create()->load($id);
                        if ($sms->getId()) {
                            $sms->addData(
                                [
                                 'content' => $sms['message'],
                                 'day'     => $sms['day'],
                                 'hour'    => $sms['hour'],
                                 'min'     => $sms['min'],
                                ]
                            )->save();
                            $smsOutPut[] = $sms->getId();
                        }
                    } else {
                        $smsArr      = [
                                        'content' => $sms['message'],
                                        'day'     => $sms['day'],
                                        'hour'    => $sms['hour'],
                                        'min'     => $sms['min'],
                                       ];
                        $sms         = $this->smsFactory->create()->setData($smsArr)->save();
                        $smsOutPut[] = $sms->getId();
                    }//end if
                }//end if
            }//end foreach
        }//end if

        return $smsOutPut;

    }//end processSMS()


    /**
     * @return string
     */
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


    /**
     * @return string
     */
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

    /**
     * @return string
     */
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
