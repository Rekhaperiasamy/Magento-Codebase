<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Customer;

/**
 * Render account front page with delete confirmation link
 *
 * Class Deletion
 * @package Scommerce\Gdpr\Controller\Customer
 */
class Deletion extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Customer\Model\Session */
    private $session;

    /** @var \Magento\Framework\View\Result\PageFactory */
    private $resultPageFactory;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (! ($this->helper->isEnabled() && $this->helper->isDeletionEnabledOnFrontend())) {
            return $this->_forward('no-route');
        }
        if (! $this->session->isLoggedIn()) {
            return $this->_redirect('customer/account/login', ['_current' => true]);
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Delete Account'));
        return $resultPage;
    }
}
