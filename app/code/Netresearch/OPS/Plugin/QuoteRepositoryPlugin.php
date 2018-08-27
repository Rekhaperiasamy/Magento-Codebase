<?php
/**
 * Netresearch_OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2017 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 *
 * QuoteRepositoryPlugin.php
 *
 * @category  OPS
 * @package   Netresearch_OPS
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 */

namespace Netresearch\OPS\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;

class QuoteRepositoryPlugin
{

    /** @var CheckoutSession */
    private $checkoutSession;


    public function __construct(
        CheckoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritdoc}
     */
    public function aroundGetActive(
        \Magento\Quote\Model\QuoteRepository $subject,
        \Closure $proceed,
        $cartId,
        array $sharedStoreIds = []
    ) {

        if ($this->checkoutSession->getPaymentRetryFlow() === true) {
            $quote = $subject->get($cartId, $sharedStoreIds);
        } else {
            $quote = $proceed($cartId, $sharedStoreIds);
        }

        return $quote;
    }
}
