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

namespace Customweb\OgoneCw\Model\Alias;

class Handler
{
	/**
	 * @var \Customweb\OgoneCw\Model\ResourceModel\Authorization\Transaction\Collection
	 */
	protected $_transactionCollectionFactory;

	/**
	 * @var \Customweb\OgoneCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 * Fetches a list of transaction which can be used as alias transactions for the given order context.
	 *
	 * @param \Customweb\OgoneCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory $transactionCollectionFactory
	 * @param \Customweb\OgoneCw\Model\DependencyContainer $container
	 */
	public function __construct(
			\Customweb\OgoneCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory $transactionCollectionFactory,
			\Customweb\OgoneCw\Model\DependencyContainer $container
	) {
		$this->_transactionCollectionFactory = $transactionCollectionFactory;
		$this->_container = $container;
	}

	/**
	 * Fetches a list of transaction which can be used as alias transactions for the given order context.
	 *
	 * @param \Customweb_Payment_Authorization_IOrderContext $orderContext
	 * @return \Customweb\OgoneCw\Model\Authorization\Transaction[]
	 */
	public function getAliasTransactions(\Customweb\OgoneCw\Model\Authorization\OrderContext $orderContext)
	{
		return $this->_transactionCollectionFactory->create()
			->addFieldToFilter('customer_id', (int) $orderContext->getCustomerId())
			->addFieldToFilter('payment_method', $orderContext->getPaymentMethod()->getCode())
			->addFieldToFilter('alias_active', true)
			->addFieldToFilter('alias_for_display', ['notnull' => true])
			->getItems();
	}

	/**
	 * Remove aliases similar to the given one.
	 *
	 * @param \Customweb\OgoneCw\Model\Authorization\Transaction $transaction
	 */
	public function removeSimilarAliases(\Customweb\OgoneCw\Model\Authorization\Transaction $transaction)
	{
		$aliases = $this->_transactionCollectionFactory->create()
			->addFieldToFilter('customer_id', $transaction->getCustomerId())
			->addFieldToFilter('payment_method', $transaction->getPaymentMethod())
			->addFieldToFilter('alias_active', true)
			->addFieldToFilter('alias_for_display', $transaction->getAliasForDisplay())
			->addFieldToFilter('entity_id', ['neq' => $transaction->getEntityId()])
			->getItems();
		foreach ($aliases as $alias) {
			$this->removeAlias($alias);
		}
	}

	/**
	 * Removes the given alias from the database as well as the remote system, if possible.
	 *
	 * @param \Customweb\OgoneCw\Model\Authorization\Transaction $transaction
	 * @throws \Exception
	 */
	public function removeAlias(\Customweb\OgoneCw\Model\Authorization\Transaction $transaction)
	{
		$transaction->setAliasActive(false);
		if ($this->_container->hasBean('Customweb_Payment_Alias_IRemoveAdapter')) {
			$removeAdapter = $this->_container->getBean('Customweb_Payment_Alias_IRemoveAdapter');
			if (!($removeAdapter instanceof \Customweb_Payment_Alias_IRemoveAdapter)) {
				throw new \Exception("Remove adapter must be of type 'Customweb_Payment_Alias_IRemoveAdapter'");
			}
			$removeAdapter->remove($transaction->getTransactionObject());
		}
		$transaction->save();
	}

	/**
	 * Deactivates the alias. It is not selected anymore by getAliasTransactions().
	 *
	 * @param \Customweb\OgoneCw\Model\Authorization\Transaction $transaction
	 * @throws Exception
	 */
	public function deactivateAlias(\Customweb\OgoneCw\Model\Authorization\Transaction $transaction)
	{
		$transaction->setAliasActive(false);
		$transaction->save();
	}
}