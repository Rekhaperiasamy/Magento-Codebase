<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/10/2015
 * Time: 10:19
 */

namespace Magenest\AbandonedCartReminder\Model;

use Magenest\AbandonedCartReminder\Model\Resource\Mail as Resource;
use Magenest\AbandonedCartReminder\Model\Resource\Mail\Collection;

class Mail extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'abandonedcartreminder_mail';

    const STATUS_PENDING   = 1;
    const STATUS_SENT      = 2;
    const STATUS_FAIL      = 3;
    const STATUS_CANCELLED = 4;

    const XML_PATH_EMAIL_SENDER = 'abandonedcartreminder/general/email_identity';


    /**
     * Types of template
     */
    const TYPE_TEXT = 1;

    const TYPE_HTML = 2;

    protected $_vars = [];

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Magento\Newsletter\Model\Queue\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magenest\AbandonedCartReminder\Model\Mail\TransportBuilder
     */
    protected $_magenestTransportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_file;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Resource $resource,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        Collection $resourceCollection,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Newsletter\Model\Queue\TransportBuilder $transportBuilder,
        \Magenest\AbandonedCartReminder\Model\Mail\TransportBuilder $mntransportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;

        $this->_transportBuilder = $transportBuilder;

        $this->_magenestTransportBuilder = $mntransportBuilder;

        $this->_scopeConfig = $scopeConfig;

        $this->_storeManager = $storeManager;

        $this->_file = $file;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }//end __construct()


    public function getIsRuleProcessed($rule_id, $key)
    {
        return $this->getResource()->getIsRuleProcessed($rule_id, $key);

    }//end getIsRuleProcessed()


    public function getStoreId()
    {
        if ($this->getData('store_id')) {
            return $this->getData('store_id');
        } else {
            return 1;
        }

    }//end getStoreId()


    public function afterLoad()
    {
        // set var for the email to prepare for send
        parent::afterLoad();
        return $this;

    }//end afterLoad()


    public function getVars()
    {
        return $this->_vars;

    }//end getVars()


    public function setVars(array $var)
    {
        $this->_vars = $var;

    }//end setVars()


    /**
     * Send email
     */
    public function send()
    {
            $this->sendMail();

    }//end send()


    protected function sendMail()
    {
        $this->_magenestTransportBuilder->setMessageContent(htmlspecialchars_decode($this->getContent()), $this->getSubject());

        $attachments = [];
        $storeScope  = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $attachedFiles = unserialize($this->getData('attachments'));

        if (is_array($attachedFiles) && !empty($attachedFiles)) {
            foreach ($attachedFiles as $attachUri) {
                $anaArr        = explode('/', $attachUri);
                $theIndex      = (count($anaArr) - 1);
                $fileName      = $anaArr[$theIndex];
                $body          = $this->_file->read($attachUri);
                $attachments[] = [
                                  'body' => $body,
                                  'name' => $fileName,
                                 ];
            }
        }

        $this->_magenestTransportBuilder->setTemplateOptions(
            [
             'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
             'store' => $this->getStoreId(),
            ]
        )->setTemplateVars(
            $this->getVars()
        )->setFrom(
            $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope)
        )->addTo(
            $this->getRecipientEmail(),
            $this->getRecipientName()
        );

        if ($attachments) {
            foreach ($attachments as $attachment) {
                if ($attachment) {
                    $this->_magenestTransportBuilder->createAttachment($attachment);
                }
            }
        }

        $transport = $this->_magenestTransportBuilder->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $e) {
            $this->_logger->critical($e->getMessage());
            throw $e;
        }

    }//end sendMail()


    /**
     * cancel the email in the queue
     */
    public function cancel()
    {
        return $this->setStatus(self::STATUS_CANCELLED)->save();

    }//end cancel()
}//end class
