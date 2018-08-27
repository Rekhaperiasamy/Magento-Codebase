<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Controller\Adminhtml\Colissimo\Rule;

use Colissimo\Rule\Controller\Adminhtml\Colissimo\Rule as AbstractRule;

/**
 * Class Index
 */
class Index extends AbstractRule
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Colissimo'), __('Rules'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Colissimo Rules'));
        $this->_view->renderLayout();
    }
}
