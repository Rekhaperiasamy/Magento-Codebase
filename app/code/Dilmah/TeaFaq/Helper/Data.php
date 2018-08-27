<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\TeaFaq\Helper;

/**
 * Faq Data Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * FAQ configuration path
     * @var string
     */
    const XML_PATH = 'dilmah_teafaq/';

    /**
     * Scope Config
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * CleanCategoryTitle
     * @param string $title
     * @return string
     */
    public function cleanCategoryTitle($title)
    {
        return preg_replace('/[^a-zA-Z0-9]+/', '', $title);
    }

    /**
     * Get Configuration Settings
     * @param string $key
     * @param string $scope
     * @return string|int|bool|null
     */
    public function getConfiguration($key, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(self::XML_PATH . $key, $scope);
    }
}
