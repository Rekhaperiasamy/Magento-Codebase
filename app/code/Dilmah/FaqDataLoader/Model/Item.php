<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_FaqDataLoader
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\FaqDataLoader\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Class Item
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Item
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Netstarter\StackFaq\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Item constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Netstarter\StackFaq\Model\ItemFactory $itemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Netstarter\StackFaq\Model\ItemFactory $itemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->itemFactory = $itemFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @param array $fixtures
     * @throws \Exception
     */
    public function install(array $fixtures)
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);
            $storeIds = [
                $this->storeManager->getStore('default')->getStoreId()
            ];
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;
                $this->createFaqItem($row, $storeIds);
            }
        }
    }

    /**
     * Create FAQ Item using CSV data, for admin store (0)
     *
     * @param array $data
     * @param array $stores
     * @return void
     */
    public function createFaqItem($data, $stores)
    {
        $faqItem = $this->itemFactory->create();
        $faqItem
            ->setData($data)
            ->setStores($stores)
            ->save();
    }
}
