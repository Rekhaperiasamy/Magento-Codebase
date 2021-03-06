<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Controller\Adminhtml\Rule;

use Magento\Rule\Model\Condition\AbstractCondition;

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

        $model = $this->_objectManager->create($type)
                ->setId($id)
                ->setType($type)
                ->setRule($this->_objectManager->create('FME\Geoipultimatelock\Model\Rule'))
                ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        $this->getResponse()
                ->setBody($html);
    }
}
