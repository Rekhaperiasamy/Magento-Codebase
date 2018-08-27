<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_ZopimChat
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\ZopimChat\Block;

use Magento\Framework\View\Element\Template;

class ZopimChat extends Template
{
    /**
     * Scope Config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Sender email config path
     */
    const ZOPIM_ACCESS_KEY = 'zopim_chat_config/zopim/key';

    /**
     * ZopimChat constructor.
     * @param Template\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * Returns the KEY for Zopim Chat Engine.
     * @return String`
     */
    public function getZopimChatKey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::ZOPIM_ACCESS_KEY, $storeScope);
    }
}
