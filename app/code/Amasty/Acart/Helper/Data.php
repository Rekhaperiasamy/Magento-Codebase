<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const CONFIG_PATH_DEBUG_MODE_EMAIL_DOMAINS = 'amasty_acart/debug/debug_emails';

    const CONFIG_PATH_DEBUG_MODE_ENABLE = 'amasty_acart/debug/debug_enable';

    protected $_scopeConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getScopeValue($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isDebugMode()
    {
        return $this->getScopeValue(self::CONFIG_PATH_DEBUG_MODE_ENABLE);
    }

    /**
     * @return array
     */
    public function getDebugEnabledEmailDomains()
    {
        return $this->isDebugMode() ? explode(',', $this->getScopeValue(self::CONFIG_PATH_DEBUG_MODE_EMAIL_DOMAINS))
            : [];
    }
}