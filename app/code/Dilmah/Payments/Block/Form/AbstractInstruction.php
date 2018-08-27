<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Block\Form;

/**
 * Abstract class for NTB payment method form
 */
abstract class AbstractInstruction extends \Magento\Payment\Block\Form
{
    /**
     * Instructions text
     *
     * @var string
     */
    protected $_instructions;

    /**
     * Get instructions text from config
     *
     * @return null|string
     */
    public function getInstructions()
    {
        if ($this->_instructions === null) {
            /** @var \Magento\Payment\Model\Method\AbstractMethod $method */
            $method = $this->getMethod();
            $this->_instructions = $method->getConfigData('instructions');
        }
        return $this->_instructions;
    }
}
