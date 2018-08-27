<?php
namespace Netresearch\OPS\Helper;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG
 *          (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DirectDebit extends \Netresearch\OPS\Helper\Payment\DirectLink\Request
{

    /**
     * @var \Netresearch\OPS\Model\AliasFactory
     */
    protected $oPSAliasFactory;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @param \Magento\Customer\Model\Session      $customerSession
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param Payment\Request                      $oPSPaymentRequestHelper
     * @param Data                                 $oPSHelper
     * @param Quote                                $oPSQuoteHelper
     * @param Order                                $oPSOrderHelper
     * @param \Netresearch\OPS\Helper\Alias        $oPSAliasHelper
     * @param \Netresearch\OPS\Model\AliasFactory  $oPSAliasFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Quote $oPSQuoteHelper,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Netresearch\OPS\Model\AliasFactory $oPSAliasFactory
    ) {
        parent::__construct(
            $customerSession,
            $oPSConfigFactory,
            $oPSPaymentRequestHelper,
            $oPSHelper,
            $oPSQuoteHelper,
            $oPSOrderHelper
        );
        $this->oPSAliasHelper = $oPSAliasHelper;
        $this->oPSAliasFactory = $oPSAliasFactory;
    }

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

        $saveAlias = $this->oPSAliasFactory->create()->load($alias, 'alias')->getId();
        $params = [
            'PM'                     => $order->getPayment()->getAdditionalInformation('PM'),
            'BRAND'                  => $order->getPayment()->getAdditionalInformation('BRAND'),
            'ALIAS'                  => $order->getPayment()->getAdditionalInformation('alias'),
            'ALIASPERSISTEDAFTERUSE' => $saveAlias ? 'Y' : 'N',
        ];

        return $params;
    }
}
