<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/10/2015
 * Time: 10:54
 */
namespace Magenest\AbandonedCartReminder\Model\Mail;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{

    protected $body;

    protected $subject;


    public function getMessage()
    {
        return $this->message;

    }//end getMessage()


    /**
     * @param $params
     * @return $this
     */
    public function createAttachment($params)
    {
        $type     = isset($params['type']) ? $params['type'] : \Zend_Mime::TYPE_OCTETSTREAM;
        $encoding = isset($params['encoding']) ? $params['encoding'] : \Zend_Mime::TYPE_OCTETSTREAM;

        $this->message->createAttachment(
            $params['body'],
            $type,
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            $encoding,
            $params['name']
        );

        return $this;

    }//end createAttachment()


    public function prepare()
    {
        return $this->prepareMessage();

    }//end prepare()


    public function setMessageContent($body, $subject)
    {
        $this->body    = $body;
        $this->subject = $subject;

    }//end setMessageContent()


    /**
     * Prepare message
     *
     * @return $this
     */
    protected function prepareMessage()
    {
        $this->message->setMessageType('text/html')->setBody($this->body)->setSubject($this->subject);

        return $this;

    }//end prepareMessage()
}//end class
