<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Rule;

class Countries extends \Magento\Backend\App\Action
{

    protected $_resultLayoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context);
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {

        $resultLayout = $this->_resultLayoutFactory->create();

        return $resultLayout;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }
}
