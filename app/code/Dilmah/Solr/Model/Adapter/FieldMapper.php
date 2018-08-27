<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Solr
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Solr\Model\Adapter;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Customer\Model\Session as CustomerSession;
use Magento\Solr\Model\Adapter\FieldType;
use Magento\Solr\Model\Adapter\FieldMapper as MFieldMapper;

/**
 * Class FieldMapper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FieldMapper extends MFieldMapper
{
	/**
     * @param Config $eavConfig
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ResolverInterface $localeResolver
     * @param CustomerSession $customerSession
     * @param FieldType $fieldType
     * @param array $localesMapping
     * @param array $supportedLang
     */
    public function __construct(
        Config $eavConfig,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ResolverInterface $localeResolver,
        CustomerSession $customerSession,
        FieldType $fieldType,
        array $localesMapping = [],
        array $supportedLang = []
    ) {
        parent::__construct($eavConfig, $registry, $storeManager, $scopeConfig, $localeResolver, $customerSession,
	        $fieldType, $localesMapping, $supportedLang);
    }

    /**
     * Hack to return language as "en"
     * Prepare language suffix for text fields.
     * For not supported languages prefix _def will be returned.
     *
     * @param array $context
     * @return string
     */
    public function getLanguageSuffix($context)
    {
        $localeCode = isset($context['localeCode'])
            ? $context['localeCode']
            : $this->scopeConfig->getValue(
                $this->localeResolver->getDefaultLocalePath(),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        if (isset($this->localesMapping[$localeCode])) {
            return $this->localesMapping[$localeCode];
        }
        $localeCode = substr($localeCode, 0, 2);
        if (in_array($localeCode, $this->supportedLang, true)) {
        	if ($localeCode != 'en'){
		        $localeCode = 'en';
	        }
            return $localeCode;
        }
        return 'def';
    }

}
