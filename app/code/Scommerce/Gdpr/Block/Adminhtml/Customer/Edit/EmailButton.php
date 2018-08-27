<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Adminhtml\Customer\Edit;

/**
 * Class EmailButton
 * @package Scommerce\Gdpr\Block\Adminhtml\Customer\Edit
 */
class EmailButton extends GenericButton
{
    /**
     * @inheritdoc
     */
    protected function getLabel()
    {
        return __('Send Deletion link To Customer');
    }

    /**
     * @inheritdoc
     */
    protected function getAction()
    {
        return 'email';
    }
}
