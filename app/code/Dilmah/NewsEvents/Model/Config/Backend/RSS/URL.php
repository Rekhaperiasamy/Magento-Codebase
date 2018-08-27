<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_NewsEvents
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\NewsEvents\Model\Config\Backend\RSS;


use Magento\Framework\Exception\LocalizedException;

class URL extends \Magento\Framework\App\Config\Value
{
    /**
     * Error keys
     */
    const INVALID_URL = 'invalidUrl';

    /**
     * URL Validate function
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $value = preg_replace('/\s+/', '', $this->getValue());
        if ($value !='' && !\Zend_Uri::check($value)) {
            throw new LocalizedException(__('Incorrect web address is included: "%1".', $value));
        }
        return $this;
    }
}
