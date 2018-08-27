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
namespace Blackbird\Monetico\Model\Config\Source;

use Magento\Framework\Locale\Bundle\LanguageBundle;

class AvailableLocales implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * Allowed countries
     */
    protected $allowedLocales = [
        'DE',
        'EN',
        'ES',
        'FR',
        'IT',
        'JA',
        'NL',
        'PT',
        'SV',
    ];

    /**
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param string $locale
     */
    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        $locale = null
    ) {
        $this->localeResolver = $localeResolver;
        if ($locale !== null) {
            $this->localeResolver->setLocale($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptionLocales();
    }

    /**
     * Retrieve the allowed locales
     *
     * @return array
     */
    public function getAllowedLocales()
    {
        return $this->allowedLocales;
    }

    /**
     * @return array
     */
    protected function getOptionLocales()
    {
        $currentLocale = $this->localeResolver->getLocale();
        $locales = \ResourceBundle::getLocales('') ?: [];
        $languages = (new LanguageBundle())->get($currentLocale)['Languages'];

        $options = [];
        $processed = [];

        foreach ($locales as $locale) {
            $langValue = strtoupper(substr($locale, 0, 2));

            if (!in_array($langValue, $this->getAllowedLocales()) || in_array($langValue, $processed)) {
                continue;
            }
            $language = \Locale::getPrimaryLanguage($locale);
            if (!$languages[$language]) {
                continue;
            }

            $options[] = [
                'value' => $langValue,
                'label' => ucwords(\Locale::getDisplayLanguage($locale, $locale)) . ' / ' . $languages[$language],
            ];

            $processed[] = $langValue;
        }

        return $this->_sortOptionArray($options);
    }

    /**
     * @param array $option
     * @return array
     */
    protected function _sortOptionArray($option)
    {
        $data = [];
        foreach ($option as $item) {
            $data[$item['value']] = $item['label'];
        }
        asort($data);
        $option = [];
        foreach ($data as $key => $label) {
            $option[] = ['value' => $key, 'label' => $label];
        }

        return $option;
    }
}
