<?php
/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Form;

/**
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Alias extends \Netresearch\OPS\Block\Form
{
    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    /**
     * Alias constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Netresearch\OPS\Helper\Alias $oPSAliasHelper
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\Session $customerSession,
        \Netresearch\OPS\Model\Config $oPSConfig,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $paymentConfig,
            $checkoutSession,
            $jsonEncoder,
            $customerSession,
            $oPSConfig,
            $oPSHelper,
            $oPSAliasHelper,
            $data
        );
        $this->oPSPaymentHelper = $oPSPaymentHelper;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netresearch_OPS::ops/form/alias.phtml');
    }

    /**
     * get available aliases for current customer
     * will return empty array if there is no current user
     *
     * @return array|\Netresearch\OPS\Model\ResourceModel\Alias\Collection
     */
    public function getAvailableAliases()
    {
        $customer = $this->customerSession->getCustomer();
        if (0 < $customer->getId()) {
            $quote = $this->oPSPaymentHelper->getQuote();
            return $this->oPSPaymentHelper->getAliasesForCustomer($customer->getId(), $quote);
        }
        return [];
    }

    /**
     * @param object $alias- the human readable alias
     * @return string
     */
    protected function getHumanReadableAlias($alias)
    {
        $aliasString = __('Credit Card Type') . ' ' . __($alias->getBrand());
        $aliasString .= ' ' . __('AccountNo') . ' ' . __($alias->getPseudoAccountOrCCNo());
        $aliasString .= ' ' . __('Expiration Date') . ' ' . $alias->getExpirationDate();
        return $aliasString;
    }
}
