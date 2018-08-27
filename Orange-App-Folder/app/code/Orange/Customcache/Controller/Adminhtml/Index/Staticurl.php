<?php
namespace Orange\Customcache\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Webapi\Exception;
use \Magento\Framework\App\Cache\TypeListInterface;
use \Magento\Framework\App\Cache\Frontend\Pool;
use Psr\Log\LoggerInterface;

class Staticurl extends Action
{
    /** @var  LoggerInterface */
    protected $_logger;

    /** @var  Pool */
    protected $_cacheFrontendPool;

    /** @var  TypeListInterface */
    protected $_cacheTypeList;

    public function __construct(
        Context $context,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        LoggerInterface $logger
    )
    {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_logger = $logger;
    }

    public function execute()
    {
        try {
            $this->createURL();
            $types = array(
                'config',
                'layout',
                'block_html',
                'collections',
                'reflection',
                'db_ddl',
                'eav',
                'config_integration',
                'config_integration_api',
                'full_page',
                'translate',
                'config_webservice'
            );
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            $this->getMessageManager()->addSuccessMessage(__('Create custom SOHO URL for all STATIC Pages Successfully !!!.'));

        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage(__('There is an Error while creating SOHO URL for all STATIC pages. '));
            $this->_logger->critical("STATIC SOHO URL Error" . $e->getMessage(), ['Exception' => $e,]);
        }
        return $this->_redirect('adminhtml/cache');
    }

    public function createURL()
    {
        try {
            exec('php bin/magento orange:regensohourl -s1 -t static');
            exec('php bin/magento orange:regensohourl -s2 -t static');
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage(__('There is an Error while creating SOHO URL for all STATIC pages. '));
            $this->_logger->critical("STATIC SOHO URL Error" . $e->getMessage(), ['Exception' => $e,]);
        }
        return true;
    }
}