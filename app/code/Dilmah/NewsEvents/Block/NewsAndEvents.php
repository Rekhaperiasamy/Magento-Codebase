<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_NewsEvents
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\NewsEvents\Block;

class NewsAndEvents extends \Magento\Framework\View\Element\Template
{
    /**
     * RSS feeds for home and full feed
     */
    const XML_PATH_RSS_FEEDS_HOME = 'news_and_events/rss_feed/feed_url_home';
    const XML_PATH_RSS_FEEDS_FULL = 'news_and_events/rss_feed/feed_url';

    /**
     * Scope of the store
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Result page factory
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * NewsAndEvents constructor.
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     * Function getNewsItems
     *
     * 
     * @param string $mode
     * @return void
     */
    public function getNewsItems($mode = 'full')
    {
        try {
            $webScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
            $xmlPath = $mode == 'full' ? self::XML_PATH_RSS_FEEDS_FULL : self::XML_PATH_RSS_FEEDS_HOME;
            $rssFeedPath = $this->scopeConfig->getValue($xmlPath, $webScope);
            if (!empty($rssFeedPath)) {
                $channel = new \Zend_Feed_Rss($rssFeedPath);
                $count = 1;
                foreach ($channel as $newsItem) {
                    echo $this->getLayout()->createBlock(
                        'Dilmah\NewsEvents\Block\News\Item',
                        'news_' . $count,
                        ['data' => ['newsItem' => $newsItem]]
                    )->toHtml();
                    $count++;
                }
            } else {
                throw new \Exception('URL is Empty');
            }
        } catch (\Exception $e) {
            echo $mode == 'full' ? "Unable to read the news source." : '';
        }
    }
}
