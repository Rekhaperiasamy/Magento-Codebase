<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Theme
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Theme\Helper;

/**
 * Class Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param string $string
     * @param int $limit
     * @param string $pad
     *
     * @return string
     */
    public function truncate($string, $limit, $pad = '...')
    {
        $limitTxt = substr($string, 0, $limit).(strlen($string) > $limit ? $pad : '');

        return $limitTxt;
    }

    /**
     * Check if current url is url for home page
     *
     * @return bool
     */
    public function isHomePage()
    {
        $currentUrl = $this->_getUrl('', ['_current' => true]);
        $urlRewrite = $this->_getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $currentUrl == $urlRewrite;
    }
}
