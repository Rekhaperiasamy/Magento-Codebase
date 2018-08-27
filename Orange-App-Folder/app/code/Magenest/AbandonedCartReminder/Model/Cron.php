<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/09/2015
 * Time: 15:32
 */

namespace Magenest\AbandonedCartReminder\Model;

use Psr\Log\LoggerInterface as Logger;

use Magento\Framework\App\ObjectManager;

class Cron 
{
    const XML_PATH_ENABLE = 'abandonedcartreminder/mandrill/enable';

    protected $birthdayModel;

    protected $abandonedCartModel;

    protected $logger;

    protected $mailFactory;
	
	protected $abandonFactory;
	
	protected $_quoteFactory;

    /**
     * @var SmsFactory
     */
    protected $smsFactory;

    protected $notificationFactory;

    protected $helper;
	
	protected $_resource;


    public function __construct(
        \Magenest\AbandonedCartReminder\Model\Processor\Birthday $birthdayProcessors,
        Logger $loggerInterface,
        \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory,
		\Magenest\AbandonedCartReminder\Model\AbandonedCartFactory $abandonedCartFactory,
		\Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magenest\AbandonedCartReminder\Model\SmsFactory $smsFactory,
        \Magenest\AbandonedCartReminder\Model\NotificationFactory $notificationFactory,
        \Magenest\AbandonedCartReminder\Helper\Connector $helper,
		\Magento\Framework\App\ResourceConnection $resource,
        \Magenest\AbandonedCartReminder\Model\Processor\AbandonedCart $abandonedCart
    ) {
        $this->birthdayModel      = $birthdayProcessors;
        $this->abandonedCartModel = $abandonedCart;

        $this->mailFactory = $mailFactory;
        $this->smsFactory  = $smsFactory;

        $this->helper              = $helper;
        $this->logger              = $loggerInterface;
        $this->notificationFactory = $notificationFactory;
		
		$this->abandonFactory = $abandonedCartFactory;
		
		$this->_quoteFactory = $quoteFactory;
		
		$this->_resource = $resource;

    }//end __construct()


    /**
     * invoke both hourly and daily. it is useful for testing purpose
     */
    public function emulate()
    {
	    $this->daily();
        $this->hourly();
        

    }//end emulate()


    /**
     * collect the abandoned cart
     */
    public function hourly()
    {
        $this->abandonedCartModel->run();

    }//end hourly()


    public function daily()
    {
        $this->birthdayModel->run();

    }//end daily()
	
	
	
	public function flushAbandonCart() {
	    $abandonfactoryCreate = $this->abandonFactory->create();
        $totalMailCollection = $this->mailFactory->create()->getCollection()
		                              ->addFieldToFilter('is_deleted',array('neq' => 'yes'))
		                              ->addFieldToFilter('created_at',array('lt' => date("Y-m-d H:i:s", strtotime('-30 day'))));
		
        foreach($totalMailCollection as $deletemail) {
		     $abandonfactoryCreate = $abandonfactoryCreate->getCollection()->addFieldToFilter('quote_id',$deletemail->getQuoteId())->getFirstItem();
			 $abandonfactoryCreate->load($abandonfactoryCreate->getId());
			 $abandonfactoryCreate->setIsDeleted('yes')->save();
			 $deleted = $deletemail->load($deletemail->getId());
			 $deleted->setIsDeleted('yes')->save();   //deleted mail collection
        }		
		
    }


    public function sendScheduledMail()
    {
        $enableMandrill = $this->helper->getEnableMandrill();
        $mailCollection = $this->mailFactory->create()->getCollection()->getMailsNeedToBeSent();

        if ($mailCollection->getSize() > 0) {
            /*
                * @var  $mail \Magenest\AbandonedCartReminder\Model\Mail
            */
			$objectManager = ObjectManager::getInstance();
            foreach ($mailCollection as $mail) {
                // push the mail to the notification box of customer
                $now = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
				$connection  = $this->_resource->getConnection();
				$mainTable = $connection->getTableName('quote');
				$select = $connection->select()->from(
					['m' => $mainTable],
					'm.*'
				)->where('m.entity_id=?',$mail->getData('quote_id'));

				$rows = $connection->fetchAssoc($select);
				$quote = $this->_quoteFactory->create();
				
				if (!$rows) {
				  continue;
				}
				
				foreach ($rows as $row) {
					$quote->setData($row);
				 }
				 
				
                $objectManager->get('Orange\Selligent\Helper\Selligent')->doSelligentAbandonCartCall($quote, $mail->getData('resume_link'));

             /*   $notification = $this->notificationFactory->create()->setData(
                    [
                     'mail_id'         => $mail->getId(),
                     'severity'        => '0',
                     'is_read'         => 0,
                     'is_trash'        => 0,
                     'store_id'        => 0,
                     'recipient_email' => $mail->getData('recipient_email'),
                     'subject'         => $mail->getData('subject'),
                     'send_date'       => $now,
                    ]
                )->save();*/
				
				$mailfactorycollection = $mail->load($mail->getId());
				
				$mailfactorycollection->setStatus(2)->save();

               // if ($enableMandrill) {
                    // send email via mandrill
                 //   $this->helper->sendEmail($mail);
               // } else {
                  //  $mail->send();
               // }

                // if enable nexmo then send information via nexmo
            }//end foreach
        }//end if

    }//end sendScheduledMail()


    /**
     * Schedule send SMS message using NexMo service
     */
    public function scheduleSendSMS()
    {
        $smsCollection = $this->smsFactory->create()->getCollection()->getSmsNeedToBeSent();

        if ($smsCollection->getSize() > 0) {
            /*
                * @var $sms \Magenest\AbandonedCartReminder\Model\Sms
            */
            foreach ($smsCollection as $sms) {
                try {
                    $sms->send();
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }

    }//end scheduleSendSMS()
}//end class
