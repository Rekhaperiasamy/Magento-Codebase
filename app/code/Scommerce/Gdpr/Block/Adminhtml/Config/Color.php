<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Adminhtml\Config;

/**
 * Class Color
 * @package Scommerce\Gdpr\Block\Adminhtml\Config
 */
class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= sprintf('<script type="text/javascript">
            require(["jquery", "jquery/colorpicker/js/colorpicker"], function ($) {
                $(function() {
                    var $el = $("#%s");
                    $el.css("backgroundColor", "#%s");

                    // Attach the color picker
                    $el.ColorPicker({
                        color: "%s",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val(hex);
                        }
                    });
                });
            });
            </script>', $element->getHtmlId(), $value, $value);
        return $html;
    }
}
