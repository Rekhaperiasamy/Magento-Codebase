<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 14/10/2015
 * Time: 13:58
 */
namespace Magenest\AbandonedCartReminder\Model\Aggregator;

class AbandonedCart implements AggregatorInterface
{
    const ABANDONED_CART_PERIOD = "abandonedcartreminder/abandonedcart/time_range";

    protected $_resource;

    protected $_quotesFactory;
    protected $_ruleFactory;

    protected $_abandonedCartFactory;
    /**
 * @var  $_abandonedCartResource \Magenest\AbandonedCartReminder\Model\Resource\AbandonedCart
*/
    protected $_abandonedCartResource;

    protected $_scopeConfig;
    protected $_abandonedCartTime = "60";

    /**
 * @var \Magenest\AbandonedCartReminder\Helper\Data
*/
    protected $helper;
    public function __construct(
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory $quotesFactory,
        \Magenest\AbandonedCartReminder\Model\AbandonedCartFactory $abandonedCartFactory,
        \Magenest\AbandonedCartReminder\Model\Resource\AbandonedCart $abandonedCartResource,
        \Magenest\AbandonedCartReminder\Helper\Data $dataHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
    
        $this->_quotesFactory = $quotesFactory;

        $this->_abandonedCartFactory = $abandonedCartFactory;

        $this->_ruleFactory = $ruleFactory;
        $this->_abandonedCartResource = $abandonedCartResource;

        $this->_scopeConfig = $scopeConfig;

        $this->helper = $dataHelper;

        $abandonedCartPeriod = $this->helper->getAbandonedCartPeriod();
        $this->_abandonedCartTime =$abandonedCartPeriod;

    }

    /**
     * collect abandoned cart
     */

    public function collect()
    {
        /**
 * @var  $quoteCollection   \Magento\Reports\Model\ResourceModel\Quote\Collection
*/

        $abandonedCarts = $this->_collectAbandonedCart();
        return $abandonedCarts;


        // return $this->_getAbandonedCart();
    }

    public function _collectAbandonedCart()
    {
        $abandonedCarts = $this->_abandonedCartResource->getAbandonedCartForInsertOperation($this->_abandonedCartTime);

        //insert it in to record
        foreach ($abandonedCarts as $quote) {
            $abandonedCart = $this->_abandonedCartFactory->create();
            $abandonedCart->setData('quote_id', $quote['entity_id'])->save();
        }

        return $abandonedCarts;

    }
}
