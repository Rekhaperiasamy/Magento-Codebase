<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Data;

use Magento\Framework\DataObject\IdentityInterface;
use Scommerce\Gdpr\Api\Data\ConsentInterface;

/**
 * Class Consent
 * @package Scommerce\Gdpr\Model\Data
 */
class Consent extends \Magento\Framework\Model\AbstractModel implements ConsentInterface, IdentityInterface
{
    const CACHE_TAG = 'scommerce_gdpr_consent'; // Cache tag

    /** @var string Prefix of model events names */
    protected $_eventPrefix = 'scommerce_gdpr_consent';

    /** @var string Parameter name in event. Use $observer->getEvent()->getObject() in observe method */
    protected $_eventObject = 'scommerce_gdpr_consent';

    /** @var string Name of object id field */
    protected $_idFieldName = self::CONSENT_ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Scommerce\Gdpr\Model\ResourceModel\Consent');
        $this->setIdFieldName(self::CONSENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getConsentId()
    {
        return $this->_getData(self::CONSENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setConsentId($value)
    {
        return $this->setData(self::CONSENT_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteId()
    {
        return $this->_getData(self::WEBSITE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteId($value)
    {
        return $this->setData(self::WEBSITE_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getGuestEmail()
    {
        return $this->_getData(self::GUEST_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setGuestEmail($value)
    {
        return $this->setData(self::GUEST_EMAIL, $value);
    }

    /**
     * @inheritdoc
     */
    public function getSource()
    {
        return $this->_getData(self::SOURCE);
    }

    /**
     * @inheritdoc
     */
    public function setSource($value)
    {
        return $this->setData(self::SOURCE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getRemoteIp()
    {
        return $this->_getData(self::REMOTE_IP);
    }

    /**
     * @inheritdoc
     */
    public function setRemoteIp($value)
    {
        return $this->setData(self::REMOTE_IP, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }
}
