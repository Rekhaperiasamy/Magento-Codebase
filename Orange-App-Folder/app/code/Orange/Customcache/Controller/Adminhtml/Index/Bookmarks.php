<?php
namespace Orange\Customcache\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Webapi\Exception;
use Psr\Log\LoggerInterface;

class Bookmarks extends Action
{
    /** @var  LoggerInterface */
    protected $_logger;

    public function __construct(
        Context $context,
        LoggerInterface $logger
    )
    {
        parent::__construct($context);
        $this->_logger = $logger;
    }

    /**
     * Dispatch request
     *
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('ui_bookmark');
            $sql = "TRUNCATE TABLE " . $tableName;
            $result = $connection->query($sql);
            if ($result) {
                $this->getMessageManager()->addSuccessMessage(__('Bookmark Table Truncated !!! '));
                $this->_logger->info("Bookmark Table Truncated");
            } else {
                $this->getMessageManager()->addErrorMessage(__('There is an Error while truncate Bookmark Table '));
            }
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
            $this->_logger->critical("Truncate ui_bookmark Error", ['Exception' => $e,]);
        }
        //redirect to Cache Mgmt page
        return $this->_redirect('adminhtml/cache');
    }
}