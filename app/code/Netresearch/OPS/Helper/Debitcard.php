<?php
/**
 * @author      Paul Siedler <paul.siedler@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Helper;

class Debitcard extends Creditcard
{
    protected function getPaymentSpecificParams(\Magento\Sales\Model\Order $order)
    {
        $params = parent::getPaymentSpecificParams($order);
        if ($this->getConfig()->getCreditDebitSplit($order->getStoreId())) {
            $params['CREDITDEBIT'] = 'D';
        }
        return $params;
    }
}
