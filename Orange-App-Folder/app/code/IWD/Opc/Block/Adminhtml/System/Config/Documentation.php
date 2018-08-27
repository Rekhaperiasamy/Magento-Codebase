<?php
namespace IWD\Opc\Block\Adminhtml\System\Config;

class Documentation extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_userGuideUrl = "https://www.iwdagency.com/help/m2-one-page-checkout/";

    protected function _getElementHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    )
    {
        $element->getValue();
        return sprintf(
            "<span style='display: block; margin-bottom: -8px;'>
                    <a href='%s' target='_blank'>%s</a></span>",
            $this->_userGuideUrl, __("User Guide")
        );
    }
}