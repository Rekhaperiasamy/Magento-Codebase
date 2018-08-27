<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Helper;

class Creditcard extends \Netresearch\OPS\Helper\Payment\DirectLink\Request
{
    protected $aliasHelper = null;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Netresearch\OPS\Model\AliasFactory
     */
    protected $oPSAliasFactory;

    /**
     * Creditcard constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param Payment\Request $oPSPaymentRequestHelper
     * @param Data $oPSHelper
     * @param Quote $oPSQuoteHelper
     * @param Order $oPSOrderHelper
     * @param Alias $oPSAliasHelper
     * @param \Netresearch\OPS\Model\AliasFactory $oPSAliasFactory
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Quote $oPSQuoteHelper,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Netresearch\OPS\Model\AliasFactory $oPSAliasFactory,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        parent::__construct(
            $customerSession,
            $oPSConfigFactory,
            $oPSPaymentRequestHelper,
            $oPSHelper,
            $oPSQuoteHelper,
            $oPSOrderHelper
        );
        $this->oPSAliasHelper  = $oPSAliasHelper;
        $this->localeResolver  = $localeResolver;
        $this->oPSAliasFactory = $oPSAliasFactory;
    }

    /**
     * @param \Netresearch\OPS\Helper\Alias $aliasHelper
     */
    public function setAliasHelper($aliasHelper)
    {
        $this->aliasHelper = $aliasHelper;
    }

    /**
     * @return \Netresearch\OPS\Helper\Alias
     */
    public function getAliasHelper()
    {
        if (null === $this->aliasHelper) {
            $this->aliasHelper = $this->oPSAliasHelper;
        }
        return $this->aliasHelper;
    }

    /**
     * @param $quote
     * @param $requestParams
     */
    public function handleAdminPayment(\Magento\Quote\Model\Quote $quote, $requestParams)
    {
        return $this;
    }

    protected function getPaymentSpecificParams(\Magento\Sales\Model\Order $order)
    {
        $alias = $order->getPayment()->getAdditionalInformation('alias');
        if (null === $alias && $this->getDataHelper()->isAdminSession()) {
            $alias = $this->getAliasHelper()->getAlias($order);
        }

        $saveAlias =  $this->oPSAliasFactory->create()->load($alias, 'alias')->getId();
        $params =  [
            'ALIAS' => $alias,
            'ALIASPERSISTEDAFTERUSE' => $saveAlias ? 'Y' : 'N',
        ];

        if ($this->getConfig()->getCreditDebitSplit($order->getStoreId())) {
            $params['CREDITDEBIT'] = 'C';
        }

        if (is_numeric($order->getPayment()->getAdditionalInformation('cvc'))) {
            $params['CVC'] = $order->getPayment()->getAdditionalInformation('cvc');
        }

        $requestParams3ds = [];
        $methodCode = $order->getPayment()->getMethod();
        if ($this->getConfig()->get3dSecureIsActive($methodCode) && false == $this->getDataHelper()->isAdminSession()) {
            $requestParams3ds = [
                'FLAG3D'           => 'Y',
                'WIN3DS'           => \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_DIRECTLINK_WIN3DS,
                'LANGUAGE'         => $this->localeResolver->getLocale(),
                'HTTP_ACCEPT'      => '*/*',
                'HTTP_USER_AGENT'  => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)',
                'ACCEPTURL'        => $this->getConfig()->getAcceptUrl(),
                'DECLINEURL'       => $this->getConfig()->getDeclineUrl(),
                'EXCEPTIONURL'     => $this->getConfig()->getExceptionUrl(),
            ];
        }
        $params = array_merge($params, $requestParams3ds);

        return $params;
    }
}
