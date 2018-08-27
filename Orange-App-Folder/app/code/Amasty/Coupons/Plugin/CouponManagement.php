<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Coupons
 */


namespace Amasty\Coupons\Plugin;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponManagement
{

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Amasty\Coupons\Helper\Data
     */
    protected $amHelper;
	protected $_logger;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository Quote repository.
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Amasty\Coupons\Helper\Data $helper,
		\Psr\Log\LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->amHelper = $helper;
		$this->_logger = $logger;
    }

    public function afterGet($subject, $result)
    {
        $appliedCoupons = $this->amHelper->getRealAppliedCodes();
        if (is_array($appliedCoupons)) {
            return implode(',', $this->amHelper->getRealAppliedCodes());
        } else {
            return $result;
        }
    }

    public function aroundSet($subject, $proceed, $cartId, $couponCode)
    {
        $proceed($cartId, $couponCode);
		$quote = $this->quoteRepository->getActive($cartId);		
        return $this->afterGet($subject, $cartId);
    }
}
