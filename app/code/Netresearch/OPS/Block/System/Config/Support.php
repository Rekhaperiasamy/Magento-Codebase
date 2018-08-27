<?php
namespace Netresearch\OPS\Block\System\Config;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Support extends \Magento\Backend\Block\Template implements RendererInterface
{
    protected $_template = 'Netresearch_OPS::ops/system/config/support.phtml';
    protected $_downloadLogPath = 'adminhtml/admin/downloadlog';

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
    }

    protected function getConfig()
    {
        return $this->oPSConfigFactory->create();
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $fieldset
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $fieldset)
    {
        $originalData = $fieldset->getOriginalData();
        $this->addData(['fieldset_label' => $fieldset->getLegend()]);
        return $this->toHtml();
    }

    /**
     * get extension version
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        return (string) $this->moduleList->getOne('Netresearch_OPS')['setup_version'];
    }

    /**
     * get support mail address
     *
     * @return string
     */
    public function getSupportMail()
    {
        $mail = $this->getConfig()->getConfigData('support_mail');
        if (!strpos($mail, '@') === false) {
            return $mail;
        }
    }

    /**
     * if we have a link to documentation
     *
     * @return int
     */
    public function hasDocumentation()
    {
        return strlen($this->getDocLinkDe() . $this->getDocLinkEn());
    }

    /**
     * get URL of German documentation
     *
     * @return string
     */
    public function getDocLinkDe()
    {
        $link = $this->getConfig()->getConfigData('doc_link_de');
        if (!strpos($link, '://') === false) {
            return $link;
        }
    }

    /**
     * get URL of English documentation
     *
     * @return string
     */
    public function getDocLinkEn()
    {
        $link = $this->getConfig()->getConfigData('doc_link_en');
        if (!strpos($link, '://') === false) {
            return $link;
        }
    }

    /**
     * if we have link to a FAQ
     *
     * @return int
     */
    public function hasFaq()
    {
        return strlen($this->getFaqLinkDe() . $this->getFaqLinkEn());
    }
    /**
     * get URL of German FAQ
     *
     * @return string
     */
    public function getFaqLinkDe()
    {
        $link = $this->getConfig()->getConfigData('faq_link_de');
        if (!strpos($link, '://') === false) {
            return $link;
        }
    }

    /**
     * get URL of English FAQ
     *
     * @return string
     */
    public function getFaqLinkEn()
    {
        $link = $this->getConfig()->getConfigData('faq_link_en');
        if (!strpos($link, '://') === false) {
            return $link;
        }
    }

    /**
     * if we use a prefix for parameter ORDERID
     *
     * @return bool
     */
    public function hasDevPrefix()
    {
        return 0 < strlen($this->getDevPrefix());
    }

    /**
     * get prefix for parameter ORDERID
     *
     * @return string
     */
    public function getDevPrefix()
    {
        return $this->getConfig()->getConfigData('devprefix');
    }

    /**
     * get link for ops.log download action
     *
     * @return string
     */
    public function getLogDownloadLink()
    {
        return $this->getUrl($this->_downloadLogPath);
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }
}
