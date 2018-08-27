<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_OPS
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\OPS\Model;

/**
 * Config model
 */
class Config extends \Netresearch\OPS\Model\Config
{
    /**
     * @return string
     */
    public function getRedirectMessage()
    {
        return __('You will be redirected to finalize your payment.');
    }

}
