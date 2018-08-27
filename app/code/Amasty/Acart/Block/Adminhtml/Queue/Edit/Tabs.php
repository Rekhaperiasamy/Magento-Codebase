<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Block\Adminhtml\Queue\Edit;

use Amasty\Acart\Controller\RegistryConstants;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected $_coreRegistry = null;
    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        \Amasty\Acart\Helper\Data $helper,
        array $data = []
    ) {

        $this->setId('queue_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Queue View'));

        $this->_coreRegistry = $registry;
        $this->_helper = $helper;

        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }
}