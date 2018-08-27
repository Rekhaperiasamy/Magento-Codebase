<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Helper\Payment\DirectLink;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface RequestInterface
{
    /**
     * sets the data helper
     *
     * @param \Netresearch\OPS\Helper\Data $dataHelper
     */
    public function setDataHelper(\Netresearch\OPS\Helper\Data $dataHelper);

    /**
     * @return \Netresearch\OPS\Model\Config
     */
    public function getConfig();

    /**
     * sets the quote helper
     *
     * @param \Netresearch\OPS\Helper\Quote $quoteHelper
     */
    public function setQuoteHelper(\Netresearch\OPS\Helper\Quote $quoteHelper);

    /**
     * extracts the parameter for the direct link request from the quote,
     * order and, optionally from existing request params
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Model\Order $order
     * @param array $requestParams
     *
     * @return array - the parameters for the direct link request
     */
    public function getDirectLinkRequestParams(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Sales\Model\Order $order,
        $requestParams = []
    );

    /**
     * @param null $config
     */
    public function setConfig($config);

    /**
     * sets the order helper
     *
     * @param \Netresearch\OPS\Helper\Order $orderHelper
     */
    public function setOrderHelper(\Netresearch\OPS\Helper\Order $orderHelper);

    public function setRequestHelper(\Netresearch\OPS\Helper\Payment\Request $requestHelper);

    /**
     * gets the order helper
     *
     * @return \Netresearch\OPS\Helper\Order
     */
    public function getOrderHelper();

    /**
     * gets the data helper
     *
     * @return \Netresearch\OPS\Helper\Data
     */
    public function getDataHelper();

    /**
     * @return \Netresearch\OPS\Helper\Request
     */
    public function getRequestHelper();

    /**
     * special handling like validation and so on for admin payments
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param $requestParams
     *
     * @return mixed
     */
    public function handleAdminPayment(\Magento\Quote\Model\Quote $quote, $requestParams);

    /**
     * gets the quote helper
     *
     * @return \Netresearch\OPS\Helper\Quote
     */
    public function getQuoteHelper();
}
