<?php
/**
 * Created by JetBrains PhpStorm.
 * User: michael
 * Date: 23.07.13
 * Time: 09:04
 * To change this template use File | Settings | File Templates.
 */

namespace Netresearch\OPS\Block\System\Config;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Kwixoconfiguration extends \Magento\Backend\Block\Template implements RendererInterface
{
    protected $_template = 'Netresearch_OPS::ops/system/config/kwixoconfiglinks.phtml';

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $fieldset
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $fieldset)
    {
        return $this->toHtml();
    }
}
