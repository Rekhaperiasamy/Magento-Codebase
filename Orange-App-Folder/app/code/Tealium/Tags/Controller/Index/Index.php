<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tealium\Tags\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;

class Index extends \Magento\Framework\App\Action\Action
{
   protected $_resultPageFactory;
 
     protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
      
    }
}
