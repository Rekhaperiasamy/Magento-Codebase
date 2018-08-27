<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Bundle
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Bundle\Plugin\Block\Sales\Order\Items;

/**
 * Class Renderer
 */
class Renderer
{
    /**
     * @param \Magento\Bundle\Block\Sales\Order\Items\Renderer $subject
     * @param \Closure $proceed
     * @param mixed $selection
     * @return string
     * @SuppressWarnings("unused")
     */
    public function aroundGetValueHtml(
        \Magento\Bundle\Block\Sales\Order\Items\Renderer $subject,
        \Closure $proceed,
        $selection
    ) {
        if ($attributes = $subject->getSelectionAttributes($selection)) {
            return sprintf('%d', $attributes['qty']) . ' x ' . $subject->escapeHtml($selection->getName());
        } else {
            return $subject->escapeHtml($selection->getName());
        }
    }
}
