<?php
/**
 * An extension to fix the bug on GitHub issue #13929
 *
 * @category  Sitewards
 * @package   Sitewards_Fix13929
 * @copyright Copyright (c) Sitewards GmbH (http://www.sitewards.com)
 * @contact   mail@sitewards.com
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Dilmah_Symblink',
    __DIR__
);
