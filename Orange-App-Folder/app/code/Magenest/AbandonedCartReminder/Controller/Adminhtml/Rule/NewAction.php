<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/09/2015
 * Time: 23:24
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;
use Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

class NewAction extends Rule
{


    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $type          = $this->getRequest()->getParam('type');
        $model         = $this->_objectManager->create('Magenest\AbandonedCartReminder\Model\Rule');
        $model         = $this->_objectManager->create('Magento\CatalogRule\Model\Rule');
        $saleRuleModel = $this->_objectManager->create('Magento\SalesRule\Model\Rule');
        $this->coreRegistry->register('current_promo_catalog_rule', $model);
        $this->coreRegistry->register('current_promo_sale_rule', $saleRuleModel);

        $this->coreRegistry->register('current_fue_rule', $model);
        $this->coreRegistry->register('type', $type);

        /*
            * @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_AbandonedCartReminder::abandonedcartreminder');
        $resultPage->getConfig()->getTitle()->prepend(__('Follow up Emails'));
        $resultPage->getConfig()->getTitle()->prepend(__('Rule'));

        // $resultPage->addPageLayoutHandles(['followup_condition_tab']);
        // $resultPage->addDefaultHandle('followup_condition_tab');
        // $resultPage->getConfig()->setPageLayout('followup_condition_tab');
        // $resultPage->getLayout()->getUpdate()->removeHandle('abandonedcartreminder_rule_new');
        // $resultPage->addContent($resultPage->getLayout()->createBlock('Magenest\AbandonedCartReminder\Block\Adminhtml\Rule\Add'));
        return $resultPage;

    }//end execute()

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::rule');

    }//end _isAllowed()
}//end class
