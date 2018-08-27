<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_SpecialOrders
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\SpecialOrders\Controller\Index;

/**
 * Class Post
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'special_orders/email/recipient_email';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'special_orders/email/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'special_orders/email/email_template';

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;

    /**
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * Post constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Captcha\Helper\Data $helper
     * @param \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
        \Magento\Customer\Model\Session $session
    ) {
        parent::__construct($context);
        $this->_transportBuilder     = $transportBuilder;
        $this->inlineTranslation     = $inlineTranslation;
        $this->scopeConfig           = $scopeConfig;
        $this->_helper               = $helper;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->_session              = $session;
    }

    /**
     * Post special order
     *
     * @return void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('*/*/');
            return;
        }

        $this->inlineTranslation->suspend();
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);

            $error = false;

            if (!\Zend_Validate::is(trim($post['first_name']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['last_name']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['telephone']), 'NotEmpty')) {
                $error = true;
            }
            if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                $error = true;
            }
            if ($error) {
                throw new \Exception();
            }

            // validate for incorrect captcha
            $this->validateCaptcha();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;


            $recipients = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);
            if (empty($recipients)) {
                throw new \Exception(__('Recipient address is not set. Please contact our hotline.'));
            }

            $from = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope);
            $template = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope);

            //send to list of recipients
            foreach (explode(',', $recipients) as $recipient) {
                if (\Zend_Validate::is(trim($recipient), 'EmailAddress')) {
                    $transport = $this->_transportBuilder
                        ->setTemplateIdentifier($template)
                        ->setTemplateOptions(
                            [
                                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                            ]
                        )
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($from)
                        ->addTo(trim($recipient))
                        ->setReplyTo($post['email'])
                        ->getTransport();

                    $transport->sendMessage();
                }
            }

            $this->inlineTranslation->resume();
            $this->messageManager->addSuccess(
                __('Thanks for sending us your order request. We\'ll respond to you very soon.')
            );
            $this->_redirect('special-orders/*');
            return;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            // @codingStandardsIgnoreStart
            $message = empty($e->getMessage()) ? __('We can\'t process your request right now. Sorry, that\'s all we know.') : $e->getMessage();
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError(
                $message
            );
            $this->_redirect('special-orders/*');
            return;
        }
    }

    /**
     * validate the captcha and throw execption for invalid captcha
     *
     * Function validateCaptcha
     * @return void
     * @throws \Exception
     */
    protected function validateCaptcha()
    {
        $formId       = 'special_orders';
        $captchaModel = $this->_helper->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            if (!$captchaModel->isCorrect($this->captchaStringResolver->resolve($this->getRequest(), $formId))) {
                $this->_session->setSpecialOrderFormData($this->getRequest()->getPostValue());
                throw new \Exception(__('Incorrect CAPTCHA'));
            }
        }
    }
}
