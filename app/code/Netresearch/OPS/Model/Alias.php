<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    OPS
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 *

 */

namespace Netresearch\OPS\Model;

/**
 * @codingStandardsIgnoreStart
 *
 * Class \Netresearch\OPS\Model\Alias
 *
 * @method string getBrand()
 * @method string getAlias()
 * @method string getPseudoAccountOrCcNo()
 * @method string getCardHolder()
 * @method string getExpirationDate()
 *
 */
class Alias extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Alias constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Alias|null $resource
     * @param ResourceModel\Alias\Collection|null $resourceCollection
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Netresearch\OPS\Model\ResourceModel\Alias $resource = null,
        \Netresearch\OPS\Model\ResourceModel\Alias\Collection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netresearch\OPS\Model\ResourceModel\Alias');
    }

    /**
     * @param int $customerId - the id of the customer
     * @param string|null  $methodCode
     * @param string|null $billingAddressHash - optional billing address hash
     * @param string|null $shippingAddressHash
     * @param int|null $storeId
     * @return \Netresearch\OPS\Model\ResourceModel\Alias\Collection - collection of aliases for given customer
     */
    public function getAliasesForCustomer(
        $customerId,
        $methodCode,
        $billingAddressHash  = null,
        $shippingAddressHash = null,
        $storeId = null
    ) {
        $collection = $this->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        if (null !== $billingAddressHash && null !== $shippingAddressHash) {
            $collection
                ->addFieldToFilter('billing_address_hash', $billingAddressHash)
                ->addFieldToFilter('shipping_address_hash', $shippingAddressHash)
                ->addFieldToFilter('state', \Netresearch\OPS\Model\Alias\State::ACTIVE)
                ->addFieldToFilter('payment_method', $methodCode);
            if($storeId) {
                $collection->addFieldToFilter('store_id', ['eq' => $storeId, 'null' => 'null']);
            }
             $collection->setOrder('created_at', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);
            ;
        }


        return $collection->load();
    }
}
