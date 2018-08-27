<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model\Config\Source;

/**
 * Class PrivacySource
 * @package Scommerce\Gdpr\Model\Config\Source
 */
class PrivacySource implements \Magento\Framework\Option\ArrayInterface
{
    const NEWSLETTER = 'Newsletter';
    const CONTACT_US = 'Contact Us';
    const CHECKOUT = 'Order';
    const REGISTRATION = 'Registration';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NEWSLETTER,   'label' => self::NEWSLETTER],
            ['value' => self::CONTACT_US,   'label' => self::CONTACT_US],
            ['value' => self::CHECKOUT,     'label' => self::CHECKOUT],
            ['value' => self::REGISTRATION, 'label' => self::REGISTRATION],
        ];
    }
}
