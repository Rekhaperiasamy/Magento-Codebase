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

namespace Customweb\OgoneCw\Api;

/**
 * Transaction repository interface.
 *
 * A Ogone transaction is an entity that holds information about the payment.
 * @api
 */
interface TransactionRepositoryInterface
{
    /**
     * Lists transactions that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @return \Customweb\OgoneCw\Api\Data\TransactionSearchResultInterface Transaction search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified transaction.
     *
     * @param int $id The transaction ID.
     * @return \Customweb\OgoneCw\Api\Data\TransactionInterface Transaction interface.
     */
    public function get($id);

    /**
     * Loads a specified transaction by payment id.
     *
     * @param string $id The payment ID.
     * @return \Customweb\OgoneCw\Api\Data\TransactionInterface Transaction interface.
     */
    public function getByPaymentId($id);

    /**
     * Loads a specified transaction by order id.
     *
     * @param int $id The order ID.
     * @return \Customweb\OgoneCw\Api\Data\TransactionInterface Transaction interface.
     */
    public function getByOrderId($id);
}