<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_NewsEvents
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\NewsEvents\Block\News;

class Item extends \Magento\Framework\View\Element\Template
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'news/item.phtml';

    /**
     * News item
     *
     * @var []
     */
    protected $item;

    /**
     * Helper for string operations
     *
     * @var \Dilmah\Theme\Helper\Data
     */
    protected $themeHelper;

    /**
     * NewsItem constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Dilmah\Theme\Helper\Data $helper
     * @param  [] $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Dilmah\Theme\Helper\Data $helper,
        $data = []
    ) {
        $this->item = $data['newsItem'];
        $this->themeHelper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Title from news Item
     *
     * @return String
     */
    public function getTitle()
    {
        return strtolower($this->item->title());
    }

    /**
     * Image Path from news Item
     * @return String
     */
    public function getImage()
    {
        return $this->item->image->img['src'];
    }

    /**
     * Description from news Item
     * @return string
     */
    public function getDescription()
    {
        $description = $this->themeHelper->truncate(strip_tags(trim($this->item->description())), 100);
        return $description;
    }

    /**
     * Link to actual news Item
     * @return string
     */
    public function getLink()
    {
        return $this->item->link();
    }
}
