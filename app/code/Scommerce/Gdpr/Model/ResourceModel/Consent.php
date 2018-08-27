<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\ResourceModel;

use Scommerce\Gdpr\Api\Data\ConsentInterface;

/**
 * Class Consent
 * @package Scommerce\Gdpr\Model\ResourceModel
 */
class Consent extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ConsentInterface::TABLE, ConsentInterface::CONSENT_ID);
    }
}
