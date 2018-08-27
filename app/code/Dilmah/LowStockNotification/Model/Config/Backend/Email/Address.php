<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * System config email field backend model
 */
namespace Dilmah\LowStockNotification\Model\Config\Backend\Email;

use Magento\Framework\Exception\LocalizedException;

class Address extends \Magento\Framework\App\Config\Value
{
    /**
     * Multiple Email Validate Edit
     * @return $this
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function beforeSave()
    {
        $value = preg_replace('/\s+/', '', $this->getValue());
        $emailList = explode(',', $value);
        foreach ($emailList as $email) {
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                throw new LocalizedException(__('Incorrect Email Address is included: "%1".', $value));
            }
        }
        $this->setValue($value);
        return $this;
    }
}
