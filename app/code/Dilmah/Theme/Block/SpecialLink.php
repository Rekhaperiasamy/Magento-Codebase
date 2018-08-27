<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Theme
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
/**
 * Front end helper block to add links.
 */

namespace Dilmah\Theme\Block;

/**
 * Class SpecialLink.
 */
class SpecialLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('special-orders');
    }
}
