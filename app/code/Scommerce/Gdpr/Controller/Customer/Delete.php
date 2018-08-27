<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Customer;

/**
 * Self-delete customer account action from front
 *
 * Class Delete
 * @package Scommerce\Gdpr\Controller\Customer
 */
class Delete extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Customer\Model\Session */
    private $session;

    /** @var \Scommerce\Gdpr\Model\Service\Account */
    private $account;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Scommerce\Gdpr\Model\Service\Account $account
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Scommerce\Gdpr\Model\Service\Account $account,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        $this->session = $session;
        $this->account = $account;
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
            $this->session->setBeforeAuthUrl($this->helper->getDeleteUrl());
            return $this->_redirect('customer/account/login', ['_current' => true]);
        }
        try {
            $this->account->anonymize($this->session->getCustomerData());
            $this->session->destroy([]);
            $this->messageManager->addSuccessMessage($this->helper->getSuccessMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->_redirect('/');
    }
}
