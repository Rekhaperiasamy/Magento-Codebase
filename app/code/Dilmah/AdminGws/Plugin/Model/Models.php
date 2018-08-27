<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_AdminGws
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\AdminGws\Plugin\Model;

/**
 * Models limiter
 * @SuppressWarnings("unused")
 */
class Models extends \Magento\AdminGws\Model\Models
{
    /**
     * @param \Magento\AdminGws\Model\Models $subject
     * @param \Closure $proceed
     * @return void
     * @todo  remove this function after Magento fix the issue-https://account.magento.com/cases/index/view/case/570968
     */
    public function aroundCoreStoreGroupSaveAfter(
        \Magento\AdminGws\Model\Models $subject,
        \Closure $proceed
    ) {
        if ($this->_role->getIsAll()) {
            return;
        }
        //$model = $this->store->getStoreGroup();
        //if ($model->getId() && !$this->_role->hasStoreGroupAccess($model->getId())) {
            $this->_role->setStoreGroupIds(
                array_unique(array_merge($this->_role->getStoreGroupIds()))
            );
        //}
    }
}
