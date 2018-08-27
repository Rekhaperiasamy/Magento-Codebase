<?php
/**
 * \Netresearch\OPS\Block\Form\DirectDebit
 *
 * @package   OPS
 * @copyright 2017 Netresearch GmbH & Co. KG <http://www.netresearch.de>
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @license   OSL 3.0
 */

namespace Netresearch\OPS\Block\Form;

use Magento\Backend\Model\Session\QuoteFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\View\Element\Template\Context;
use \Netresearch\OPS\Model\Config as PaymentConfig;

class DirectDebit extends \Magento\Payment\Block\Form
{
    /**
     * Backend Payment Template
     */
    const TEMPLATE = 'Netresearch_OPS::ops/form/directDebit.phtml';

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Backend\Model\Session\QuoteFactory
     */
    protected $backendSessionQuoteFactory;

    /**
     * @var PaymentConfig
     */
    protected $config;


    /**
     * DirectDebit constructor.
     *
     * @param Context        $context
     * @param PaymentConfig  $oPSConfig
     * @param CountryFactory $countryFactory
     * @param QuoteFactory   $backendSessionQuoteFactory
     * @param array          $data
     */
    public function __construct(
        Context $context,
        PaymentConfig $oPSConfig,
        CountryFactory $countryFactory,
        QuoteFactory $backendSessionQuoteFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->countryFactory = $countryFactory;
        $this->backendSessionQuoteFactory = $backendSessionQuoteFactory;
        $this->config = $oPSConfig;
        $this->setTemplate(self::TEMPLATE);
    }

    /**
     * get ids of supported countries
     *
     * @return array
     */
    public function getDirectDebitCountryIds()
    {
        return explode(',', $this->config->getDirectDebitCountryIds());
    }

    /**
     * gets the previously entered billing country (if any)
     *
     * @return string - empty string if no country is given, otherwise the country
     */
    public function getCountry()
    {
        $country = '';
        $quote = $this->getBackendSessionQuote();
        if ($quote->getBillingAddress()) {
            $country = $quote->getBillingAddress()->getCountryId();
        }

        return $country;
    }

    /**
     * @param string $countryCode
     *
     * @return string
     */
    public function getCountryNameByCode($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        if ($country->getId()) {
            return $country->getName();
        }

        return $countryCode;
    }

    /**
     * @return \Magento\Backend\Model\Session\Quote
     */
    public function getBackendSessionQuote()
    {
        return $this->backendSessionQuoteFactory->create()->getQuote();
    }
}
