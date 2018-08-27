<?php

namespace Orange\Customcache\Block\Adminhtml\Index;

class Index extends \Magento\Backend\Block\Widget\Container
{
    public function __construct(\Magento\Backend\Block\Widget\Context $context,array $data = [])
    {
        parent::__construct($context, $data);
    }
	
		
	public function getMagentoCachePurgeUrl()
    {
        return $this->getUrl('customcache/index/index/');
    }
	
	public function getFastlyCachePurgeUrl()
    {
        return $this->getUrl('customcache/index/fastly/');
    }

    public function getTruncateBookmarksUrl()
    {
        return $this->getUrl('customcache/index/bookmarks/');
    }

    public function getStaticSohoURL() {
        return $this->getUrl('customcache/index/staticurl/');
    }

    public function getOtherSohoURL() {
        return $this->getUrl('customcache/index/othersoho/');
    }

	public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }


}
