<?php
namespace Orange\Seo\Controller\Adminhtml\Salespad;
use Magento\Framework\App\ObjectManager;
class Downloadfr extends \Magento\Framework\App\Action\Action
{
	
	
	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    protected $siteMap;
	protected $directory_list;
    
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
		\Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Magento\Sitemap\Model\Sitemap $siteMap
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
		$this->directory_list = $directory_list;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->sitemap = $siteMap;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
	$objectManager = ObjectManager::getInstance();
	$doc_root =   $this->directory_list->getRoot();
	$path          = $doc_root . '/app/i18n/orange/fr_fr/';
	$pFileName     = 'fr_FR.csv';
	$downloadfile  = $path . $pFileName;
    $attachment = file_get_contents($downloadfile);
	header('Content-type: text/xml');
    header('Content-Disposition: attachment; filename="'.$pFileName.'"');
    echo $attachment;
	exit;
    }
}
