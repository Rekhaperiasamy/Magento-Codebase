<?php
namespace Orange\Coupon\Block\Adminhtml\Import;

class Edit extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'import.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }
}