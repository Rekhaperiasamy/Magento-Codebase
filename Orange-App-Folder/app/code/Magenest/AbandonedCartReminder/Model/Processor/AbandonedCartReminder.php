<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/09/2015
 * Time: 15:42
 */

namespace Magenest\AbandonedCartReminder\Model\Processor;

use Magento\Email\Model\Template as EmailTemplateModel;

abstract class AbandonedCartReminder
{
    const XML_PATH_BCC_NAME  = 'abandonedcartreminder/general/bcc_name';
    const XML_PATH_BCC_EMAIL = 'abandonedcartreminder/general/bcc_email';

    /**
     * @var object
     */
    protected $_emailTarget;

    protected $_activeMail;

    protected $_activeMailChain;

    protected $_activeRule;

    protected $type;

    protected $_rulesFactory;

    protected $_emailTemplateModel;

    protected $_mailFactory;

    protected $_scopeConfig;

    protected $_context;

    protected $_couponGenerator;

    protected $storeId;

    /**
     * App emulation model
     *
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $_appEmulation;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    protected $_mailData = [];

    protected $_vars;

    protected $googleAnalytic;

    /**
     * @var \Magenest\AbandonedCartReminder\Model\SmsFactory
     */
    protected $smsFactory;

    /**
     * @var \Magenest\AbandonedCartReminder\Model\MessageFactory
     */
    protected $messageFactory;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magenest\AbandonedCartReminder\Model\Resource\Rule\CollectionFactory $rulesFactory,
        \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory,
        \Magenest\AbandonedCartReminder\Model\MessageFactory $messageFactory,
        \Magenest\AbandonedCartReminder\Model\SmsFactory $smsFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        EmailTemplateModel $emailTemplateModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\SalesRule\Model\Coupon\Massgenerator $massGenerator,
        \Magento\Store\Model\App\Emulation $appEmulation
    ) {
        $this->_appEmulation = $appEmulation;
        $this->_rulesFactory = $rulesFactory;

        $this->_mailFactory        = $mailFactory;
        $this->_scopeConfig        = $scopeConfig;
        $this->_emailTemplateModel = $emailTemplateModel;
        $this->_emailTarget;

        $this->_quoteFactory    = $quoteFactory;
        $this->quoteRepository  = $cartRepositoryInterface;
        $this->_urlBuilder      = $urlInterface;
        $this->_couponGenerator = $massGenerator;

        $this->smsFactory = $smsFactory;

        $this->messageFactory = $messageFactory;

        $this->_context = $context;

    }//end __construct()


    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;

    }//end getType()


    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;

    }//end setType()


    /**
     * create follow up email rule
     */
    public function createFollowUpEmail()
    {
        if (!$this->_emailTarget) {
            return;
        }

        $rules = $this->getMatchingRule();
       
        if ($rules->getSize() > 0) {
            foreach ($rules as $rule) {
                $this->_activeRule = $rule;

                //$isValidate  = $this->isValidate($rule);
               // $isDuplidate = $this->isDuplicate($rule);

               // if ($isValidate && !$isDuplidate) {
                    $this->generateMail($this->_emailTarget, $rule);

                    $this->prepareMail($rule);
                    $this->postCreateMail();
                    // $mail->save();
                    // generate the sms if the rule define sms
                   // $this->generateSms($this->_emailTarget, $rule);
              //  }

                // get the chains
            }//end foreach
        }//end if

    }//end createFollowUpEmail()
	
	
	


    /**
     * @param $emailTarge
     * @param \Magenest\AbandonedCartReminder\Model\Rule $rule
     */
    public function generateSms($emailTarget, $rule)
    {
        $ids = $rule->getMessageChain();

        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $smsId) {
                $mobileValue        = '';
                $smsModel           = $this->smsFactory->create();
                $smsData            = [];
                $messageModel       = $this->messageFactory->create()->load($smsId);
                $smsData['status']  = 0;
                $smsData['rule_id'] = $rule->getId();
                if (method_exists($emailTarget, 'getData')) {
                    $smsData['recipient_name']   = $emailTarget->getData('recipient_name');
                } else {
                    $smsData['recipient_name'] = $emailTarget->getFirstname().' '.$emailTarget->getLastname();
                }

                //there are two possibility

                if (method_exists($emailTarget, 'getCustomAttribute')) {
                    /** @var  $mobile \Magento\Framework\Api\AttributeValue */
                    $mobile = $emailTarget->getCustomAttribute('mobile_number');

                    if (is_object($mobile)) {
                        $mobileValue = $mobile->getValue();
                    }
                }

                if (!$mobileValue && method_exists($emailTarget, 'getData')) {
                    $mobileValue = $emailTarget->getData('mobile_number');

                }
                $smsData['recipient_mobile'] = $mobileValue;
                $smsData['content'] = $messageModel->getData('content');

                $now               = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                $current_date_time = new \DateTime();
                $modify            = '+';
                $modifyDelta       = 0;

                if ($messageModel->getData('day')) {
                    $modifyDelta += ($messageModel->getData('day') * 24 * 60 * 60);
                }

                if ($messageModel->getData('hour')) {
                    $modifyDelta += ($messageModel->getData('hour') * 60 * 60);
                }

                if ($messageModel->getData('min')) {
                    $modifyDelta += ($messageModel->getData('min') * 60);
                }

                $modify = '+'.$modifyDelta.' seconds';

                $current_date_time->modify($modify);

                $smsData['scheduled_send_date']      = $current_date_time->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                $smsData['duplicated_key'] = $emailTarget->getId();
                $smsModel->setData($smsData)->save();
            }//end foreach
        }//end if

    }//end generateSms()


    /**
     * get the active rule for a type
     *
     * @return \Magenest\AbandonedCartReminder\Model\Resource\Rule\Collection
     */
    public function getMatchingRule()
    {
        /*
            * @var $collection  \Magenest\AbandonedCartReminder\Model\Resource\Rule\Collection
        */
        $collection = $this->_rulesFactory->create();
        $collection->getRulesByType($this->type);
        return $collection;

    }//end getMatchingRule()


    /**
     * @return boolean
     */
    public function getIsValidate()
    {
        return true;

    }//end getIsValidate()


    /**
     * @param $emailTarget
     * @param $rule
     */


    public function generateMail($emailTarget, $rule)
    {
        /*
            * @var  $mail \Magenest\AbandonedCartReminder\Model\Mail
        */
        $mailQueue = $this->_mailFactory->create();
        $bccName   = $this->_scopeConfig->getValue(
            self::XML_PATH_BCC_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $bccEmail  = $this->_scopeConfig->getValue(
            self::XML_PATH_BCC_EMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $mailData   = [];
        $mailChains = $rule->getMailChain();
        if ($mailChains) {
            foreach ($mailChains as $mail) {
                $this->_activeMailChain = $mail;

                $this->_mailData['status'] = 0;
                // pending
                $this->_mailData['rule_id'] = $rule->getId();
				
				$utc = $emailTarget->getId();
				$om = \Magento\Framework\App\ObjectManager::getInstance();
			    $storeId = $emailTarget->getStoreId(); 
			    $baseStoreUrl = $om->get('Magento\Store\Model\StoreManagerInterface')
						->getStore($storeId)
						->getBaseUrl(); 
                $resumeLinkWithSecurityKey = $this->_urlBuilder->getUrl('abandonedcartreminder/track/restore', ['_current' => false, 'utc' => $utc]);
				$resumeLinkWithSecurityKey = $baseStoreUrl."abandonedcartreminder/track/restore/utc/".$utc."/";
				$pattern    = '/\/\?SID.*/';
                $resumeLink = preg_replace($pattern, '', $resumeLinkWithSecurityKey);

                if (method_exists($emailTarget, 'getData')) {
                    $this->_mailData['recipient_name'] = $emailTarget->getData('customer_firstname').' '.$emailTarget->getData('customer_lastname');

                    $this->_mailData['recipient_email'] = $emailTarget->getData('customer_email');
					
					
					$this->_mailData['quote_id'] = $emailTarget->getData('entity_id');
					
					$this->_mailData['resume_link'] = $resumeLink;
                } else {
				
                    $this->_mailData['recipient_name'] = $emailTarget->getFirstname().' '.$emailTarget->getLastname();

                    $this->_mailData['recipient_email'] = $emailTarget->getEmail();
					
					$this->_mailData['quote_id'] = $emailTarget->getId();
					
					$this->_mailData['resume_link'] = $resumeLink;
                }

                $this->_mailData['bcc_name']  = $bccName;
                $this->_mailData['bcc_email'] = $bccEmail;

                // $mailData['subject'] =$mail['template'];
                // calculate the scheduled sending email
                $now               = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                $current_date_time = new \DateTime();
                $modify            = '+';
                $modifyDelta       = 0;

                if ($mail['day']) {
                    $modifyDelta += ($mail['day'] * 24 * 60 * 60);
                }

                if ($mail['hour']) {
                    $modifyDelta += ($mail['hour'] * 60 * 60);
                }

                if ($mail['min']) {
                    $modifyDelta += ($mail['min'] * 60);
                }

                $modify = '+'.$modifyDelta.' seconds';

                $this->_mailData['send_date']      = $current_date_time->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                $this->_mailData['duplicated_key'] = $this->_emailTarget->getId();
                $this->_activeMail                 = $mailQueue;
                // set vars for email template model
                $this->prepareMail();
			//	echo "gfD";
                // create the mail content
                 $emailTemplate = $this->_activeMailChain['template'];
                if ($this->storeId) {
                    $this->_appEmulation->startEnvironmentEmulation($this->storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
                }

                //$model = $this->_emailTemplateModel->load($emailTemplate);

          //      $this->insertCoupon($this->_activeRule->getData('promotion_rule_id'), $model->getTemplateText());

               // $model->setVars($this->_vars);
             //   $this->_mailData['content'] = $this->applyGoogleAnalytics($model->processTemplate(), $this->_activeRule);
                //$this->_mailData['styles'] = $model->getTemplateStyles();
              //  if ($model->getProcessedTemplateSubject([])) {
                 //   $this->_mailData['subject'] = $model->getProcessedTemplateSubject([]);
             //   }
                if ($this->storeId) {
                    $this->_appEmulation->stopEnvironmentEmulation();
               }
                //$this->_mailData['cancel_serialized'] = $this->_activeRule->getData('cancel_serialized');

                // save the attachment information
              //  $attachedFiles                  = $this->_activeRule->getData('attached_files');
              //  $this->_mailData['attachments'] = $attachedFiles;
			//	echo $this->_mailData['subject']."subject";
				//print_r($this->_mailData); 

                $mailQueue->setData($this->_mailData)->save();

                $this->_activeMail->setData('schedule_time', $modify);
            }//end foreach
        }//end if

    }//end generateMail()


    /**
     * @param $input
     * @param $rule
     */
    protected function applyGoogleAnalytics($input, $rule)
    {
        $analytics = '?utm_medium='.$rule->getData('ga_medium').'&utm_campaign='.$rule->getData('ga_campaign').'&utm_source='.$rule->getData('ga_source');

        if ($rule->getData('ga_content')) {
            $analytics .= '&utm_content='.$rule->getData('ga_content');
        }

        if ($rule->getData('ga_term')) {
            $analytics .= '&utm_term='.$rule->getData('ga_term').'&uq=';
        }

        $this->googleAnalytic = $analytics;

        $pattern = "/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i";
        $input   = preg_replace_callback($pattern, [$this, 'replaceUrlWithGA'], $input);

        return $input;

    }//end applyGoogleAnalytics()


    /**
     * @param $input
     * @return string
     */
    public function replaceUrlWithGA($input)
    {
        if (isset($input[0])) {
            return $input[0] .= '/'.$this->googleAnalytic;
        } else {
            return $input;
        }

    }//end replaceUrlWithGA()


    /**
     * @param $ruleId
     * @param $emailContent
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertCoupon($ruleId, $emailContent)
    {
        if (!$ruleId) {
            return;
        }

        // the coupon is coupon.code and coupon.expiry_date
        $pattern = '/\{\{var\s+coupon.code\}\}/';
        preg_match($pattern, $emailContent, $matches);

        if (isset($matches[0])) {
            $this->_couponGenerator->setQty(1);
            $this->_couponGenerator->setFormat('alphanum');
            $this->_couponGenerator->setLength(10);

            $this->_couponGenerator->setRuleId($ruleId);
            ;
            $this->_couponGenerator->generatePool();

            $codes = $this->_couponGenerator->getGeneratedCodes();
            $this->_vars['coupon_code'] = $codes[0];
        }

    }//end insertCoupon()


    /**
     * @param $rule
     * @return boolean
     */
    public function isDuplicate($rule)
    {
        $isDuplicate = false;
        // if there is no email Target then we return false to stop process
        if (!$this->_emailTarget) {
            return true;
        }

        $rule_id = $rule->getId();

        $key = $this->_emailTarget->getId();

        $mailModel   = $this->_mailFactory->create();
        $isDuplicate = $mailModel->getIsRuleProcessed($rule_id, $key);
        return $isDuplicate;

    }//end isDuplicate()


    /**
     * Validate a email target again a specific rule
     *
     * @return boolean
     */
    public function validateCustomerGroupAndWebsite($customerGroupId = 0, $websiteId = 0)
    {
        $isValidate = false;
        ;

        if (!$this->_emailTarget || !$this->_activeRule) {
            return false;
        } else {
            if (!$websiteId) {
                $websiteId = $this->_emailTarget->getWebsiteId();
            }

            if (!$customerGroupId) {
                $customerGroupId = $this->_emailTarget->getGroupId();
            }

            $isValidate = $this->_activeRule->isValidateTarget($customerGroupId, $websiteId);
        }

        return $isValidate;

    }//end validateCustomerGroupAndWebsite()


    /**
     * build email target and
     *
     * @return mixed
     */
    abstract public function run();


    abstract public function isValidate($rule);


    abstract public function prepareMail();


    abstract public function postCreateMail();
}//end class
