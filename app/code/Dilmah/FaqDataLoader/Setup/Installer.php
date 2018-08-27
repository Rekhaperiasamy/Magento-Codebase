<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_FaqDataLoader
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\FaqDataLoader\Setup;

use Magento\Framework\Setup;

class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * @var \Dilmah\FaqDataLoader\Model\Category
     */
    protected $category;

    /**
     * @var \Dilmah\FaqDataLoader\Model\Item
     */
    protected $item;

    /**
     * Installer constructor.
     * @param \Dilmah\FaqDataLoader\Model\Category $category
     * @param \Dilmah\FaqDataLoader\Model\Item $item
     */
    public function __construct(\Dilmah\FaqDataLoader\Model\Category $category, \Dilmah\FaqDataLoader\Model\Item $item)
    {
        $this->category = $category;
        $this->item = $item;
    }

    /**
     * Install the Data
     *
     * @return void
     */
    public function install()
    {
        $this->category->install(['Dilmah_FaqDataLoader::fixtures/faq_store_category.csv']);
        $this->item->install(['Dilmah_FaqDataLoader::fixtures/faq_store_items.csv']);
    }
}
