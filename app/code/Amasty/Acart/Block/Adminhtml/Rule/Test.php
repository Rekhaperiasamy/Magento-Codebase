<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Block\Adminhtml\Rule;

use Amasty\Acart\Controller\RegistryConstants;

class Test extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ){
        $this->_coreRegistry = $registry;

        return parent::__construct($context, $data);
    }
    public function getRule()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_AMASTY_ACART_RULE);
    }
}