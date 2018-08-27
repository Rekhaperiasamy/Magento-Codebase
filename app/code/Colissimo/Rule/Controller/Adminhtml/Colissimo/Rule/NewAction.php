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
 * Class NewAction
 */
class NewAction extends AbstractRule
{
    /**
     * New Colissimo rule action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
