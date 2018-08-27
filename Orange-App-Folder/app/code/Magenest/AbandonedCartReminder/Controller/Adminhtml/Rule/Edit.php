<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 16/10/2015
 * Time: 16:35
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

use Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;
use Magento\Framework\Controller\ResultFactory;

class Edit extends Rule
{


    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        /*
            * @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $type          = $this->getRequest()->getParam('type');
        $catalogRule   = $this->_objectManager->create('Magento\CatalogRule\Model\Rule');
        $saleRuleModel = $this->_objectManager->create('Magento\SalesRule\Model\Rule');

        $model = $this->_objectManager->create('Magenest\AbandonedCartReminder\Model\Rule')->load($params['id']);

        $saleRuleModel->setData('conditions_serialized', $model->getData('conditions_serialized'));

        // $this->coreRegistry->register('current_promo_catalog_rule', $catalogRule);
        $this->coreRegistry->register('current_promo_sale_rule', $saleRuleModel);

        $this->coreRegistry->register('current_fue_rule', $model);
        $this->coreRegistry->register('type', $type);

        $this->coreRegistry->register('rule_data', $model->getData());

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_AbandonedCartReminder::abandonedcartreminder_rule');
        $resultPage->getConfig()->getTitle()->prepend(__('Follow Up Emails'));
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Rule'));
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
