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
 * Class Category
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category
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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Category constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Netstarter\StackFaq\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Netstarter\StackFaq\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->categoryFactory = $categoryFactory;
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
                $this->createFaqCategory($row, $storeIds);
            }
        }
    }

    /**
     * @param array $data
     * @param array $stores
     * @return void
     */
    public function createFaqCategory($data, $stores)
    {
        $faqCategory = $this->categoryFactory->create();
        $faqCategory
            ->setData($data)
            ->setStores($stores)
            ->save();
    }
}
