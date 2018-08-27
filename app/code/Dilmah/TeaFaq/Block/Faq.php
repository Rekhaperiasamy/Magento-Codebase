<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\TeaFaq\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Dilmah\TeaFaq\Helper\Data as FaqData;

/**
 * Main FAQ block
 */
class Faq extends Template
{
    /**
     * Desc
     * @var \Dilmah\TeaFaq\Helper\Data
     */
    protected $faqHelper;

    /**
     * Page Config
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * Constructor
     * @param Context $context
     * @param FaqData $faqHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     */
    public function __construct(
        Context $context,
        FaqData $faqHelper,
        \Magento\Framework\Stdlib\StringUtils $string
    ) {
        $this->faqHelper = $faqHelper;
        $this->pageConfig = $context->getPageConfig();
        $this->string = $string;
        parent::__construct($context);
    }

    /**
     * Add title, keywords & meta description to head
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $title = $this->faqHelper->getConfiguration('settings/config_dtfaq_title');
        $keywords = $this->faqHelper->getConfiguration('settings/config_dtfaq_keywords');
        $metaDescription = $this->faqHelper->getConfiguration('settings/config_dtfaq_meta_description');
        $canonicalUrl = $this->faqHelper->getConfiguration('settings/config_dtfaq_canonical_url');
        if ($title) {
            //$blockTitle = $this->getLayout()->getBlock('page.main.title');
            //$blockTitle->setPageTitle($this->faqHelper->getConfiguration('settings/config_faq_title'));
            $this->pageConfig->getTitle()->set($title);
        }
        if ($keywords) {
            $this->pageConfig->setKeywords($keywords);
        }
        if ($metaDescription) {
            $this->pageConfig->setDescription($this->string->substr($metaDescription, 0, 255));
        }
        if ($canonicalUrl) {
            $this->pageConfig->addRemotePageAsset(
                $canonicalUrl,
                'canonical',
                [
                    'attributes' => [
                        'rel' => 'canonical'
                    ]
                ]
            );
        }
        return parent::_prepareLayout();
    }
}
