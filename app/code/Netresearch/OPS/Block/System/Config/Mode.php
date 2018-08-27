<?php

/**
 * Mode.php
 * @author  paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License
 */

namespace Netresearch\OPS\Block\System\Config;

class Mode extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html =  parent::_getElementHtml($element);

        $javascript = "
        <script type=\"text/javascript\">
            require(['jquery'], function($) {
                element = $('#".$element->getHtmlId()."');
                element.on('change', function() {
                    if(element.val() != '".$element->getValue()."'){
                        $('#ops_mode_comment').css('display', 'block');
                    } else {
                        $('#ops_mode_comment').css('display', 'none');
                    }
                });
            });
        </script>";

        return $html.$javascript;
    }
}
