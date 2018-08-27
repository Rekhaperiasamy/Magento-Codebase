<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_GiftCardAccount
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\GiftCardAccount\Model;

class GiftCardExpiration extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Locale Date of Magento Instance
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Giftcare Account Factory
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $giftCardFactory;

    /**
     * GiftCardExpiration constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->giftCardFactory = $giftCardFactory;
        $this->localeDate = $localeDate;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Email addresses provider
     *
     * @param array $notificationDurationsList
     * @return array
     */
    public function getEmailsNearlyToExpired($notificationDurationsList)
    {
        $collection = $this->giftCardFactory->create()->getCollection();
        $dateFormat = "Y-m-d";
        $expireDates = [];
        $giftcardData = [];
        foreach ($notificationDurationsList as $duration) {
            $currentDate = new \DateTime('now', new \DateTimeZone($this->localeDate->getConfigTimezone()));
            $expireDates[] = $this->localeDate->date($currentDate)->modify($duration)->format($dateFormat);
        }
        if (!empty($expireDates)) {
            $select = $collection->getSelect()
                ->where('date_expires in (?)', $expireDates)
                ->where('balance > ?', 0)
                ->where('status = ?', 1)
                ->where('state = ?', 0)
                ->where('is_redeemable = ?', 1)
                ->where('store_id IS NOT NULL');
            foreach ($select->query()->fetchAll() as $giftCard) {
                $giftcardData[$giftCard['website_id']][] = $giftCard;
            }
        }
        return $giftcardData;
    }
}
