<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Api\Data;

/**
 * Interface ConsentInterface
 * @package Scommerce\Gdpr\Api\Data
 */
interface ConsentInterface
{
    const TABLE = 'scommerce_gdpr_consent'; // Db table

    const CONSENT_ID    = 'consent_id';
    const CUSTOMER_ID   = 'customer_id';
    const WEBSITE_ID    = 'website_id';
    const GUEST_EMAIL   = 'guest_email';
    const SOURCE        = 'source';
    const REMOTE_IP     = 'remote_ip';
    const CREATED_AT    = 'created_at';
    const UPDATED_AT    = 'updated_at';

    /**
     * @return int
     */
    public function getConsentId();

    /**
     * @param int $value
     * @return ConsentInterface
     */
    public function setConsentId($value);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $value
     * @return ConsentInterface
     */
    public function setCustomerId($value);

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @param int $value
     * @return ConsentInterface
     */
    public function setWebsiteId($value);

    /**
     * @return string
     */
    public function getGuestEmail();

    /**
     * @param string $value
     * @return ConsentInterface
     */
    public function setGuestEmail($value);

    /**
     * @return string
     */
    public function getSource();

    /**
     * @param string $value
     * @return ConsentInterface
     */
    public function setSource($value);

    /**
     * @return string
     */
    public function getRemoteIp();

    /**
     * @param string $value
     * @return ConsentInterface
     */
    public function setRemoteIp($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     * @return ConsentInterface
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     * @return ConsentInterface
     */
    public function setUpdatedAt($value);
}
