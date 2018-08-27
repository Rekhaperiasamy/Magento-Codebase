<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_GiftCardAccount
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\GiftCardAccount\Model\Plugin;


class CreateGiftCard
{
    /**
     * Gift card account giftcardaccount
     *
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $giftCAFactory = null;

    /**
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory
     */
    public function __construct(
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory
    ) {
        $this->giftCAFactory = $giftCAFactory;
    }

    /**
     * Create gift card account on event
     * used for event: magento_giftcardaccount_create
     *
     * @param \Magento\GiftCardAccount\Observer\CreateGiftCard $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @SuppressWarnings("unused")
     */
    public function aroundExecute(
        \Magento\GiftCardAccount\Observer\CreateGiftCard $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer
    ) {
        $data = $observer->getEvent()->getRequest();
        $code = $observer->getEvent()->getCode();
        $order = $data->getOrder() ?: ($data->getOrderItem()->getOrder() ?: null);

        $productOptions = $data->getOrderItem()->getProductOptions();
        $recipientEmail = null;
        if ($productOptions && !empty($productOptions['giftcard_recipient_email'])) {
            $recipientEmail = $productOptions['giftcard_recipient_email'];
        }

        $model = $this->giftCAFactory->create()->setStatus(
            \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED
        )->setWebsiteId(
            $data->getWebsiteId()
        )->setBalance(
            $data->getAmount()
        )->setLifetime(
            $data->getLifetime()
        )->setIsRedeemable(
            $data->getIsRedeemable()
        )->setOrder(
            $order
        )->setRecipientEmail(
            $recipientEmail
        )->setStoreId(
            $order->getStoreId()
        )->save();

        $code->setCode($model->getCode());

        return $this;
    }
}
