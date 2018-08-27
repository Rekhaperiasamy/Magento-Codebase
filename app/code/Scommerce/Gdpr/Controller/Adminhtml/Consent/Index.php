<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Adminhtml\Consent;

/**
 * Class Index
 * @package Scommerce\Gdpr\Controller\Adminhtml\Consent
 */
class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magento_Customer::manage';

    /** @var \Magento\Framework\View\Result\PageFactory */
    private $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Privacy Policy Consent'), __('Privacy Policy Consent'));
        $resultPage->getConfig()->getTitle()->prepend(__('Privacy Policy Consent'));

        return $resultPage;
    }
}
