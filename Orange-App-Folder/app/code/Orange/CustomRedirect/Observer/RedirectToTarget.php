<?php
namespace Orange\CustomRedirect\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class RedirectToTarget implements ObserverInterface
{
    protected $responseFactory;
    protected $url;
    protected $scopeConfig;
    const XML_REDIRECT_URLS = 'customredirect/general/redirect_url';
    const XML_TARGET_URL = 'customredirect/general/target_url';
    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $url
    )
    {
        $this->_responseFactory = $responseFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_url = $url;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $redirect_url = $this->scopeConfig->getValue(self::XML_REDIRECT_URLS, $storeScope);
        $target_url = $this->scopeConfig->getValue(self::XML_TARGET_URL, $storeScope);
        $current_url = $this->_url->getCurrentUrl();
        $url_part = parse_url($current_url);
        $substr = substr($url_part["path"], 0, 4);
        // prepare base URL
        if ($substr == '/nl/' || $substr == '/fr/') {
            $base_url = $this->_url->getBaseUrl();
        } else {
            $base_url = $url_part['scheme'] . "://" . $url_part['host'] . "/";
        }
        $current_path = str_replace($base_url, "", $current_url);
        $to_replace = array(
            '/(\r\n?|\n)/', // newlines
            '/\\\\\*/', // asterisks
        );
        $replacements = array(
            '|',
            '.*',
            '\1' . preg_quote($base_url, '/') . '\2'
        );
        $patterns_quoted = preg_quote($redirect_url, '/');
        $regexps = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
        if ((bool)preg_match($regexps, $current_path)) {
            $this->_responseFactory->create()->setRedirect($target_url, 301)->sendResponse();
            die();
        }
    }
}