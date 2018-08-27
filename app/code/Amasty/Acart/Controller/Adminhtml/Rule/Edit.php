<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Controller\Adminhtml\Rule;

use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Amasty\Acart\Controller\Adminhtml\Rule
{
    /**
     * Customer edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $ruleId = (int)$this->getRequest()->getParam('id');

        $ruleData = [];

        $rule = $this->_objectManager->create('Amasty\Acart\Model\Rule');
        $isExistingRule = (bool)$ruleId;

        if ($isExistingRule) {
            $rule = $rule->load($ruleId);

            if (!$rule->getId()) {
                $this->messageManager->addError(__('Something went wrong while editing the rule.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('amasty_acart/*/index');
                return $resultRedirect;
            }
        }
        
        $this->initCurrentRule($rule);

        $rule->getSalesRule()
            ->getConditions()->setJsFormObject('rule_conditions_fieldset');

        $ruleData['rule_id'] = $ruleId;

        $this->_getSession()->setRuleData($ruleData);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_Acart::acart_rule');
        $this->prepareDefaultCustomerTitle($resultPage);
        $resultPage->setActiveMenu('Amasty_Acart::acart');
        if ($isExistingRule) {
            $resultPage->getConfig()->getTitle()->prepend($rule->getName());
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Rule'));
        }


        return $resultPage;
    }
}
