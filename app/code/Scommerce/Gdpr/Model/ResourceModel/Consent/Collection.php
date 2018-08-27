<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\ResourceModel\Consent;

use Scommerce\Gdpr\Api\Data\ConsentInterface;

/**
 * Class Collection
 * @package Scommerce\Gdpr\Model\ResourceModel\Consent
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /** @var string Identifier field name for collection items. Can be used by collections with items without defined */
    protected $_idFieldName = ConsentInterface::CONSENT_ID;

    /** @var string Name prefix of events that are dispatched by model */
    protected $_eventPrefix = 'scommerce_gdpr_consent_collection';

    /** @var string Name of event parameter */
    protected $_eventObject = 'scommerce_gdpr_consent_collection';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Scommerce\Gdpr\Model\Data\Consent', 'Scommerce\Gdpr\Model\ResourceModel\Consent');
    }
}
