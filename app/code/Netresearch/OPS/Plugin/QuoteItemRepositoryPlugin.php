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

use \Magento\Framework\App\ObjectManager;
use \Magento\Quote\Model\Quote\Item\Repository;
use \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;
use \Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\Session as CheckoutSession;

class QuoteItemRepositoryPlugin
{
    /** @var \Magento\Quote\Model\QuoteRepository */
    private $quoteRepository;

    /** @var CheckoutSession */
    private $checkoutSession;

    /** @var  CartItemOptionsProcessor */
    private $cartItemOptionsProcessor;

    public function __construct(
        QuoteRepository $quoteRepository,
        CheckoutSession $checkoutSession
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
    }

    public function aroundGetList(
        Repository $subject,
        \Closure $proceed,
        $cartId
    ) {
        $output = [];
        if ($this->checkoutSession->getPaymentRetryFlow() === true) {
            $quote = $this->quoteRepository->get($cartId);

            /** @var  \Magento\Quote\Model\Quote\Item $item */
            foreach ($quote->getAllVisibleItems() as $item) {
                $item = $this->getCartItemOptionsProcessor()->addProductOptions($item->getProductType(), $item);
                $output[] = $this->getCartItemOptionsProcessor()->applyCustomOptions($item);
            }
        } else {
            $output = $proceed($cartId);
        }

        return $output;
    }

    /**
     * @return CartItemOptionsProcessor
     * @deprecated
     */
    private function getCartItemOptionsProcessor()
    {
        if (!$this->cartItemOptionsProcessor instanceof CartItemOptionsProcessor) {
            $this->cartItemOptionsProcessor = ObjectManager::getInstance()->get(CartItemOptionsProcessor::class);
        }

        return $this->cartItemOptionsProcessor;
    }
}
