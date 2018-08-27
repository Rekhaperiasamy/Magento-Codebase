<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Customer
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Customer\Block;

/**
 * Class Dashboard
 */
class Dashboard extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * Dashboard constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_rewardFactory = $rewardFactory;
        $this->_balanceFactory = $balanceFactory;
        parent::__construct($context, $data);
    }

    /**
     * Set various variables requested by template
     *
     * @return void
     */
    public function prepareTemplateData()
    {
        $customer = $this->_customerSession->getCustomer();
        $rewardInstance = $this->_rewardFactory->create()->setCustomer(
            $customer
        )->setWebsiteId(
            $this->_storeManager->getWebsite()->getId()
        )->loadByCustomer();
        $balanceInstance = $this->_balanceFactory->create()->setCustomerId(
            $customer->getId()
        )->loadByCustomer();

        $this->addData(
            [
                'points_balance' => $rewardInstance->getPointsBalance(),
                'customer_name' => $customer->getName(),
                'customer_first_name' => $customer->getFirstname(),
                'customer_email' => $customer->getEmail(),
                'store_credit' => $balanceInstance->getAmount()
            ]
        );
    }
}
