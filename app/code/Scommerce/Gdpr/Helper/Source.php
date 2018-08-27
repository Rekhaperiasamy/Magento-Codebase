<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Helper;

use \Scommerce\Gdpr\Model\Config\Source\PrivacySource;

/**
 * Class Source
 * @package Scommerce\Gdpr\Helper
 */
class Source
{
    /**
     * @return string
     */
    public function getNewsletterKey()
    {
        return PrivacySource::NEWSLETTER;
    }

    /**
     * @return string
     */
    public function getContactUsKey()
    {
        return PrivacySource::CONTACT_US;
    }

    /**
     * @return string
     */
    public function getCheckoutKey()
    {
        return PrivacySource::CHECKOUT;
    }

    /**
     * @return string
     */
    public function getRegistrationKey()
    {
        return PrivacySource::REGISTRATION;
    }
}
