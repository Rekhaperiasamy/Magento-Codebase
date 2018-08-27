<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Adminhtml\Customer\Edit;

/**
 * Class ExportButton
 * @package Scommerce\Gdpr\Block\Adminhtml\Customer\Edit
 */
class ExportButton extends GenericButton
{
    /**
     * @inheritdoc
     */
    protected function getLabel()
    {
        return __('Export GDPR Data');
    }

    /**
     * @inheritdoc
     */
    protected function getAction()
    {
        return 'export';
    }
}
