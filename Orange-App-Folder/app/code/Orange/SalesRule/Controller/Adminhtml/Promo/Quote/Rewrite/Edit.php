<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\SalesRule\Controller\Adminhtml\Promo\Quote\Rewrite;

class Edit extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote\Edit
{
	public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\SalesRule\Model\Rule');

        $this->_coreRegistry->register(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE, $model);

        $resultPage = $this->resultPageFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getRuleId()) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('sales_rule/*');
                return;
            }

            $resultPage->getLayout()->getBlock('promo_sales_rule_edit_tab_coupons')->setCanShow(true);
			$resultPage->getLayout()->getBlock('orange_promo_sales_rule_edit_tab_importcoupons')->setCanShow(true); //Enabled the custom import tab renderer
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setFormName('sales_rule_form');
        $model->getConditions()->setJsFormObject(
            $model->getConditionsFieldSetId($model->getConditions()->getFormName())
        );
        $model->getActions()->setFormName('sales_rule_form');
        $model->getActions()->setJsFormObject(
            $model->getActionsFieldSetId($model->getActions()->getFormName())
        );

        $this->_initAction();

        $this->_addBreadcrumb($id ? __('Edit Rule') : __('New Rule'), $id ? __('Edit Rule') : __('New Rule'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Cart Price Rule')
        );
        $this->_view->renderLayout();
    }
}