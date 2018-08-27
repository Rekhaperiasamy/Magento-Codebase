<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 20/05/2016
 * Time: 08:53
 */

namespace Magenest\AbandonedCartReminder\Helper;

use Mandrill_Error;
use Psr\Log\LoggerInterface as Logger;

class Connector extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLE = 'abandonedcartreminder/mandrill/enable';
    const XML_PATH_APIKEY = 'abandonedcartreminder/mandrill/api_key';

    protected $scopeConfig;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();

    }//end __construct()


    /**
     * get information that mandrill is enable or not
     *
     * @return boolean
     */
    public function getEnableMandrill()
    {
        $enable = $this->scopeConfig->getValue(self::XML_PATH_ENABLE);

        if ($enable === '1') {
            return true;
        } else {
            return false;
        }

    }//end getEnableMandrill()


    /**
     *
     */
    public function getUserInformation()
    {
        $userInfo = null;
        $apiKey   = $this->scopeConfig->getValue(self::XML_PATH_APIKEY);
        if (!$apiKey) {
            return;
        }

        $mandrill = new \Mandrill($apiKey);
        try {
            $userInfo = $mandrill->users->info();
        } catch (Mandrill_Error $me) {
            $this->_logger->critical($me->getMessage());
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $userInfo;

    }//end getUserInformation()


    public function sendEmail(\Magenest\AbandonedCartReminder\Model\Mail $mail)
    {
        $apiKey = $this->scopeConfig->getValue(self::XML_PATH_APIKEY);
        if (!$apiKey) {
            return;
        }

        $mandrill = new \Mandrill($apiKey);
        $message  = [
                     'subject'    => $mail->getData('subject'),
                     'from_name'  => $mail->getData('subject'),
                     'from_email' => $mail->getData('subject'),

                    ];

        $message['to'][] = ['email' => $mail->getData('recipient_email')];

        $message['to'][] = array(
                            'email' => $mail->getData('bcc_email'),
                            'type'  => 'bcc',
                           );

        $message['html'] = $mail->getData('content');

        $mandrill->call('messages/send', array('message' => $message));
        return;

    }//end sendEmail()
}//end class
