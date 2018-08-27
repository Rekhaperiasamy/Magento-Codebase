<?php

namespace Netresearch\OPS\Controller\Adminhtml\Admin;

class SaveAlias extends \Magento\Backend\App\Action
{
    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Netresearch\OPS\Helper\Data $oPSHelper
    ) {
        parent::__construct($context);
        $this->oPSHelper = $oPSHelper;
    }

    /**
     * Retrieve quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function _getQuote()
    {
        return $this->_objectManager->get('Magento\Backend\Model\Session\Quote')
            ->getQuote();
    }

    public function execute()
    {
        $alias = $this->_request->getParam('alias');
        $quote = $this->_getQuote();
        if (0 < strlen($alias)) {
            $payment = $quote->getPayment();
            $payment->setAdditionalInformation('alias', $alias);
            $this->oPSHelper->log('saved alias ' . $alias . ' for quote #' . $quote->getId());
            $payment->setDataChanges(true);
            $payment->save();
        }
    }
}
