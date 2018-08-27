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

abstract class Rule extends Action
{

    protected $ruleFactory;

    protected $_resultLayoutFactory;

    protected $heper;

    /**
     *  \Magento\Config\Model\Config\Source\Email\Identity
     *
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\Registry                       $coreRegistry
     * @param \Magenest\AbandonedCartReminder\Model\RuleFactory $ruleFactory
     */


    public function __construct(
        Context $context,
        Registry $coreRegistry,
        RuleFactory $ruleFactory,
        \Magenest\AbandonedCartReminder\Helper\Data $data,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->_context     = $context;
        $this->coreRegistry = $coreRegistry;
        $this->ruleFactory  = $ruleFactory;
        $this->heper        = $data;

        $this->_resultLayoutFactory = $resultLayoutFactory;
        // $this->resultFactory = $resultFactory;
        parent::__construct($context);

    }//end __construct()


    /**
     * Check ACL
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::rule');
    }
}//end class
