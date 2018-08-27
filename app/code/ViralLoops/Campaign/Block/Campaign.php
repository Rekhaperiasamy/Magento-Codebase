<?php
namespace ViralLoops\Campaign\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use ViralLoops\Campaign\Helper\Data as Helper;

class Campaign extends Template
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * Render campaign iframe and js
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helper->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
}
