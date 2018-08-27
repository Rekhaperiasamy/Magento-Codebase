<?php

namespace FME\Geoipultimatelock\Block\Adminhtml;

use Magento\Backend\Block\Widget\Tabs;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Backend\Model\Auth\Session;

class Import extends Tabs
{

    public function __construct(Context $context, EncoderInterface $encoderInterface, Session $authSession)
    {

        parent::__construct($context, $encoderInterface, $authSession);

        $this->setTemplate('import/import.phtml');
        $this->setFormAction($this->_urlBuilder->getUrl('*/import/import'));
    }

    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }
}
