<?php

namespace Netresearch\OPS\Controller\Adminhtml\Alias;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Netresearch\OPS\Model\AliasFactory
     */
    protected $oPSAliasFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Netresearch\OPS\Model\AliasFactory $oPSAliasFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Netresearch\OPS\Model\AliasFactory $oPSAliasFactory
    ) {
        parent::__construct($context);
        $this->oPSAliasFactory = $oPSAliasFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }

    public function execute()
    {
        $aliasId = $this->_request->getParam('id');
        $alias = $this->oPSAliasFactory->create()->load($aliasId);
        if ($alias->getId()) {
            $alias->delete();
            $this->messageManager->addSuccess(__('Removed alias %1.', $alias->getAlias()));
        } else {
            $this->messageManager->addError(__('Could not remove alias %1.', $alias->getAlias()));
        }
        return $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }
}
