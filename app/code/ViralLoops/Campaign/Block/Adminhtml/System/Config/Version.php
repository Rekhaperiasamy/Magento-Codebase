<?php
namespace ViralLoops\Campaign\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Config\Block\System\Config\Form\Field;

class Version extends Field
{
    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleList  = $moduleList;
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return 'v' . $this->getVersion();
    }

    /**
     * Get Module version number
     *
     * @return string
     */
    public function getVersion()
    {
        $moduleInfo = $this->moduleList->getOne($this->getModuleName());
        return $moduleInfo['setup_version'];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        $classArray = explode('\\', get_class($this));

        return count($classArray) > 2 ? "{$classArray[0]}_{$classArray[1]}" : '';
    }
}
