<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Controller\Adminhtml\Csblock;

use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Class NewConditionHtml
 * @package Aheadworks\Csblock\Controller\Adminhtml\Csblock
 */
class NewConditionHtml extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $prefix = 'conditions';
        if ($this->getRequest()->getParam('prefix')) {
            $prefix = $this->getRequest()->getParam('prefix');
        }

        $rule = 'Aheadworks\Csblock\Model\Csblock';
        if ($this->getRequest()->getParam('rule')) {
            $rule = base64_decode($this->getRequest()->getParam('rule'));
        }
        $model = $this->_objectManager->create(
            $type
        )->setId(
                $id
            )->setType(
                $type
            )->setRule(
                $this->_objectManager->create($rule)
            )->setPrefix(
                $prefix
            );
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aheadworks_Csblock::csblock');
    }
}
