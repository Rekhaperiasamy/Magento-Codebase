<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Adminhtml\Customer;

/**
 * Base action for customer
 *
 * Class AbstractAction
 * @package Scommerce\Gdpr\Controller\Adminhtml\Customer
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Scommerce_Gdpr::config';

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $customerRepository;

    /** @var \Magento\Framework\App\Response\Http\FileFactory */
    protected $fileFactory;

    /** @var \Scommerce\Gdpr\Model\Service\Account */
    protected $account;

    /** @var \Scommerce\Gdpr\Helper\Data */
    protected $helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Scommerce\Gdpr\Model\Service\Account $account
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Scommerce\Gdpr\Model\Service\Account $account,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->customerRepository = $customerRepository;
        $this->fileFactory = $fileFactory;
        $this->account = $account;
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (! $this->helper->isEnabled()) {
            return $this->_redirect('admin/dashboard/index');
        }
        $customer = $this->getCustomer();
        if (! $customer) {
            return $this->redirect();
        }

        try {
            return $this->run($customer);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->redirect();
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    abstract protected function run(\Magento\Customer\Api\Data\CustomerInterface $customer);

    /**
     * Helper method for redirects
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function redirect()
    {
        return $this->_redirect('customer/index/index', ['_current' => true]);
    }

    /**
     * Helper method for redirect to edit
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function redirectToEdit(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        return $this->_redirect('customer/index/edit', ['id' => $customer->getId(), '_current' => true]);
    }

    /**
     * Helper method for get customer via request
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|\Magento\Customer\Model\Data\Customer|false
     */
    private function getCustomer()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('No customer ID provided.'));
            $customer = false;
        }
        return $customer;
    }
}
