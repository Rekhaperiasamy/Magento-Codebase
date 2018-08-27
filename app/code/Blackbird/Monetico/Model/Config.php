<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Model;

use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Store\Model\ScopeInterface;
use Blackbird\Monetico\Model\Method\Onetime;
use Blackbird\Monetico\Model\Method\CofidisEuro;
use Blackbird\Monetico\Model\Method\CofidisTxcb;
use Blackbird\Monetico\Model\Method\CofidisFxcb;
use Blackbird\Monetico\Model\Method\PayPal;

class Config implements ConfigInterface
{
    /**
     * @var MethodInterface
     */
    protected $methodInstance;

    /**
     * Current payment method code
     *
     * @var string
     */
    protected $_methodCode;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Blackbird\Monetico\Model\Config\Source\AvailableLocales
     */
    protected $_availableLocales;

    /**
     * Current store id
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var string
     */
    protected $pathPattern;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Config\Source\AvailableLocales $availableLocales
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Blackbird\Monetico\Model\Config\Source\AvailableLocales $availableLocales
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_availableLocales = $availableLocales;
    }

    /**
     * Sets method instance used for retrieving method specific data
     *
     * @param MethodInterface $method
     * @return $this
     */
    public function setMethodInstance($method)
    {
        $this->methodInstance = $method;

        return $this;
    }

    /**
     * Sets method code
     *
     * @param string $methodCode
     * @return void
     */
    public function setMethodCode($methodCode)
    {
        switch ($methodCode) {
            case CofidisEuro::METHOD_CODE:
            case CofidisTxcb::METHOD_CODE:
            case CofidisFxcb::METHOD_CODE:
            case PayPal::METHOD_CODE:
                $methodCode = Onetime::METHOD_CODE;
                break;
        }

        $this->_methodCode = $methodCode;
    }

    /**
     * Method code setter
     *
     * @param string|MethodInterface $method
     * @return $this
     */
    public function setMethod($method)
    {
        if ($method instanceof MethodInterface) {
            $this->_methodCode = $method->getCode();
        } elseif (is_string($method)) {
            $this->_methodCode = $method;
        }

        return $this;
    }

    /**
     * Payment method instance code getter
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }

    /**
     * Store ID setter
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;

        return $this;
    }

    /**
     * Sets path pattern
     *
     * @param string $pathPattern
     * @return void
     */
    public function setPathPattern($pathPattern)
    {
        $this->pathPattern = $pathPattern;
    }

    /**
     * Returns payment configuration value
     *
     * @param string $key
     * @param null $storeId
     * @return null|string
     */
    public function getValue($key, $storeId = null)
    {
        $underscored = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $key));
        $path = $this->_getSpecificConfigPath($underscored);
        if ($path !== null) {
            $value = $this->_scopeConfig->getValue(
                $path,
                ScopeInterface::SCOPE_STORE,
                !is_null($storeId) ? $storeId : $this->_storeId
            );

            return $value;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->getValue('version');
    }

    /**
     * @return bool
     */
    public function isDebugModeEnabled()
    {
        return (bool)$this->_scopeConfig->getValue(
            'payment/monetico/debug_mode',
            ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        $locale = strtoupper(substr($this->_scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORE,
            $this->_storeId
        ), 0, 2));

        if (!in_array($locale, $this->_availableLocales->getAllowedLocales())) {
            $locale = $this->getValue('locale');
        }

        return $locale;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        if ($this->pathPattern) {
            return sprintf($this->pathPattern, $this->_methodCode, $fieldName);
        }

        return "payment/{$this->_methodCode}/{$fieldName}";
    }
}
