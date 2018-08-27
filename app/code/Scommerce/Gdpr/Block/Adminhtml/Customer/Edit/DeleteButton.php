<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Adminhtml\Customer\Edit;

/**
 * Class DeleteButton
 * @package Scommerce\Gdpr\Block\Adminhtml\Customer\Edit
 */
class DeleteButton extends GenericButton
{
    /**
     * @inheritdoc
     */
    protected function getLabel()
    {
        return __('Delete Personal Data');
    }

    /**
     * @inheritdoc
     */
    protected function getAction()
    {
        return 'delete';
    }
}
