<?php

namespace Orange\Numbervalidation\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $number = $this->getRequest()->getParam('design_te_existing_number_chk');
        $number = str_replace(array('/', ' '), array('', ''), $number);
        if ($number) {
            $model = $objectManager->create('Orange\Numbervalidation\Model\Numbervalidation')->getNumberVaidationData($number);
            $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
            $log_mode = $scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (isset($log_mode) && $log_mode == 1) {
                $objectManager->get('Orange\Upload\Helper\Data')->logCreate('/var/log/Numbervalidation.log', $model);
            }
            return $this->resultJsonFactory->create()->setData($model);
        }
    }

}
