<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Contact
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Contact\Plugin\Controller\Index;

class Post extends \Magento\Contact\Controller\Index\Post
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlFactory;

    /**
     * Post constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
                                \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\UrlFactory $urlFactory)
    {
        parent::__construct($context, $transportBuilder, $inlineTranslation, $scopeConfig, $storeManager);
        $this->urlFactory = $urlFactory;
    }


    /**
     * around plugin for Post user question
     *
     * @param \Magento\Contact\Controller\Index\Post $subject
     * @param \Closure $proceed
     * @return void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings("unused")
     */
    public function aroundExecute(
        \Magento\Contact\Controller\Index\Post $subject,
        \Closure $proceed
    ) {
        $post = $subject->getRequest()->getPostValue();
        if (!$post) {
            $subject->_redirect('*/*/');
            return;
        }

        $subject->inlineTranslation->suspend();
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
            if (!\Zend_Validate::is(trim($post['telephone']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['is_confirmed']), 'NotEmpty')) {
                $error = true;
            }
            if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                $error = true;
            }
            if ($error) {
                throw new \Exception();
            }

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $subject->_transportBuilder
                ->setTemplateIdentifier($subject->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($subject->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                ->addTo($subject->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                ->setReplyTo($post['email'])
                ->getTransport();

            $transport->sendMessage();
            $subject->inlineTranslation->resume();
            $subject->messageManager->addSuccess(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );

            $url = $this->urlFactory->create()->getUrl('contact', ['_secure' => true]);

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));

        } catch (\Exception $e) {
            $subject->inlineTranslation->resume();
            $subject->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );

            $url = $this->urlFactory->create()->getUrl('contact', ['_secure' => true]);

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
        }
    }
}
