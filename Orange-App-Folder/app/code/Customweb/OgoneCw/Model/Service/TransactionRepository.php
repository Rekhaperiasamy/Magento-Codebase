<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_OgoneCw
 * 
 */

namespace Customweb\OgoneCw\Model\Service;

class TransactionRepository implements \Customweb\OgoneCw\Api\TransactionRepositoryInterface
{
	/**
     * @var \Customweb\OgoneCw\Model\Authorization\TransactionRegistry
     */
    protected $_transactionRegistry;

    /**
     * @var \Customweb\OgoneCw\Model\Authorization\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Customweb\OgoneCw\Api\Data\TransactionInterfaceFactory
     */
    protected $_transactionDataFactory;

    /**
     * @var \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $_dataObjectHelper;

	/**
	 * @param \Customweb\OgoneCw\Model\Authorization\TransactionRegistry $transactionRegistry
	 * @param \Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\OgoneCw\Api\Data\TransactionInterfaceFactory $transactionDataFactory
	 * @param \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory
	 * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
	 */
	public function __construct(
			\Customweb\OgoneCw\Model\Authorization\TransactionRegistry $transactionRegistry,
			\Customweb\OgoneCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\OgoneCw\Api\Data\TransactionInterfaceFactory $transactionDataFactory,
			\Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory $searchResultsFactory,
			\Magento\Framework\Api\DataObjectHelper $dataObjectHelper
	) {
		$this->_transactionRegistry = $transactionRegistry;
		$this->_transactionFactory = $transactionFactory;
		$this->_transactionDataFactory = $transactionDataFactory;
		$this->_searchResultsFactory = $searchResultsFactory;
		$this->_dataObjectHelper = $dataObjectHelper;
	}

	public function get($id)
	{
		$transaction = $this->_transactionRegistry->retrieve($id);
		return $this->getDataModel($transaction);
	}

	public function getByPaymentId($id)
	{
		$transaction = $this->_transactionRegistry->retrieveByPaymentId($id);
		return $this->getDataModel($transaction);
	}

	public function getByOrderId($id)
	{
		$transaction = $this->_transactionRegistry->retrieveByOrderId($id);
		return $this->getDataModel($transaction);
	}

	public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
	{
		$searchResults = $this->_searchResultsFactory->create();
		$searchResults->setSearchCriteria($searchCriteria);
		/** @var \Customweb\OgoneCw\Model\ResourceModel\Authorization\Transaction\Collection $collection */
		$collection = $this->_transactionFactory->create()->getCollection();
		$searchResults->setTotalCount($collection->getSize());
		$sortOrders = $searchCriteria->getSortOrders();
		if ($sortOrders) {
			/** @var \Magento\Framework\Api\SortOrder $sortOrder */
			foreach ($searchCriteria->getSortOrders() as $sortOrder) {
				$collection->addOrder(
						$sortOrder->getField(),
						($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
				);
			}
		}
		$collection->setCurPage($searchCriteria->getCurrentPage());
		$collection->setPageSize($searchCriteria->getPageSize());

		$transactions = [];
		foreach ($collection as $transactionModel) {
			$transactions[] = $this->getDataModel($transactionModel);
		}
		$searchResults->setItems($transactions);
		return $searchResults;
	}

	/**
	 * Retrieve transaction model with transaction data
	 *
	 * @return \Customweb\OgoneCw\Api\Data\TransactionInterface
	 */
	protected function getDataModel(\Customweb\OgoneCw\Model\Authorization\Transaction $transaction)
	{
		$transactionDataObject = $this->_transactionDataFactory->create();
		$this->_dataObjectHelper->populateWithArray(
				$transactionDataObject,
				$this->extractTransactionData($transaction),
				'\Customweb\OgoneCw\Api\Data\TransactionInterface'
		);
		return $transactionDataObject;
	}

	private function extractTransactionData(\Customweb\OgoneCw\Model\Authorization\Transaction $transaction)
	{
		$data = $transaction->getData();

		$transactionData = [];
		if (is_array($transaction->getTransactionData())) {
			foreach ($transaction->getTransactionData() as $key => $value) {
				$transactionData[] = [
					'key' => $key,
					'value' => $value
				];
			}
		}
		$data[\Customweb\OgoneCw\Api\Data\TransactionInterface::TRANSACTION_DATA] = $transactionData;
		return $data;
	}

}