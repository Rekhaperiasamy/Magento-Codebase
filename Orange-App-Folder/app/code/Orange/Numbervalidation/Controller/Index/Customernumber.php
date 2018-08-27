<?php

namespace Orange\Numbervalidation\Controller\Index;

class Customernumber extends \Magento\Framework\App\Action\Action {

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
            $model = $objectManager->create('Orange\Numbervalidation\Model\Numbervalidation')->getcustomerNumberVaidationData($number);            
            return $this->resultJsonFactory->create()->setData($model);
        }
    }
}
