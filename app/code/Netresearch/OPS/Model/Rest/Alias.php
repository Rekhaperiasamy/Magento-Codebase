<?php

namespace Netresearch\OPS\Model\Rest;

class Alias implements \Netresearch\OPS\Api\AliasInterface
{
    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    private $aliasHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder */
    private $searchCriteriaBuilder;


    /**
     * Alias constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Netresearch\OPS\Helper\Alias $aliasHelper
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Netresearch\OPS\Helper\Alias $aliasHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    
        $this->checkoutSession = $checkoutSession;
        $this->aliasHelper = $aliasHelper;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param string $methodCode
     *
     * @return array
     */
    public function getList($methodCode)
    {
        $quote = $this->checkoutSession->getQuote();
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $quote->getCustomer();
        $aliasCollection = $this->aliasHelper->getAliasesForCustomer(
            $customer->getId(),
            $methodCode,
            $quote
        );

        return $this->toArray($aliasCollection);
    }

    /**
     * @return array
     */
    public function getListForRetryPage()
    {

        $orderId = $this->checkoutSession->getData('order_on_retry');
        $criteria = $this->searchCriteriaBuilder->addFilter(
            'increment_id',
            $orderId,
            'eq'
        )->create();
        $orderList = $this->orderRepository->getList($criteria)->getItems();
        $order = array_pop($orderList);
        /** @var \Magento\Customer\Model\Customer $customer */
        $aliasCollection = $this->aliasHelper->getAliasesForAddresses(
            $order->getCustomerId(),
            $order->getBillingAddress(),
            $order->getShippingAddress()
        );

        return $this->toArray($aliasCollection);
    }

    /**
     * @param $aliasCollection
     *
     * @return mixed[]
     */
    private function toArray($aliasCollection)
    {
        $aliasDataForCustomer = [];
        foreach ($aliasCollection as $key => $alias) {
            $brand = $alias->getBrand();
            if ($brand) {
                $aliasDataForCustomer[] = [
                    'id' => $alias->getId(),
                    'alias' => $alias->getAlias(),
                    'storedAliasBrand' => $brand,
                    'cardHolderName' => $alias->getCardHolder(),
                    'aliasCardNumber' => $alias->getPseudoAccountOrCcNo(),
                    'expirationDatePart' => substr_replace($alias->getExpirationDate(), '/', 2, 0),
                    'paymentMethod' => $alias->getPaymentMethod()
                ];
            }
        }

        return $aliasDataForCustomer;
    }
}
