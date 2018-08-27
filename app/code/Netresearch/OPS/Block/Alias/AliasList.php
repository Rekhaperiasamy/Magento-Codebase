<?php
/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Alias;

/**
 * @author     Thomas Birke <thomas.birke@netresearch.de>
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AliasList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->oPSAliasHelper = $oPSAliasHelper;
        $this->paymentHelper = $paymentHelper;
        $this->customerSession = $customerSession;
    }

    public function getAliases()
    {
        $aliases = [];
        $customer = $this->customerSession->getCustomer();
        if (0 < $customer->getId()) {
            $aliasesCollection = $this->oPSAliasHelper->getAliasesForCustomer($customer->getId());
            foreach ($aliasesCollection as $alias) {
                $aliases[] = $alias;
            }
        }
        return $aliases;
    }

    /**
     * get human readable name of payment method
     *
     * @param string $methodCode Code of payment method
     * @return string Name of payment method
     */
    public function getMethodName($methodCode)
    {
        $instance = $this->paymentHelper->getMethodInstance($methodCode);
        if ($instance) {
            return $instance->getTitle();
        }
    }

    /**
     * retrieves the url for deletion the alias
     *
     * @param $aliasId - the id of the alias
     *
     * @return string - the url for deleting the alias with the given id
     */
    public function getAliasDeleteUrl($aliasId)
    {
        return $this->getUrl(
            'ops/customer/deleteAlias/',
            [
                 'id'       => $aliasId,
                 '_secure'  => $this->getRequest()->isSecure()
            ]
        );
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/', ['_secure' => true]);
    }
}
