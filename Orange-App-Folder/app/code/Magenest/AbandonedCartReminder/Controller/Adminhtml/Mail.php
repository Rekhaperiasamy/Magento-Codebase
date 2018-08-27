<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 21:05
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

use Magenest\AbandonedCartReminder\Model\RuleFactory;

abstract class Mail extends Action
{

    protected $mailFactory;

    protected $_resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\Registry                       $coreRegistry
     * @param  \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory
     */


    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MailFactory $mailFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->_context     = $context;
        $this->coreRegistry = $coreRegistry;
        $this->mailFactory  = $mailFactory;

        $this->_resultLayoutFactory = $resultLayoutFactory;

        // $this->resultFactory = $resultFactory;
        parent::__construct($context);

    }//end __construct()

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::mail');

    }//end _isAllowed()
}//end class
