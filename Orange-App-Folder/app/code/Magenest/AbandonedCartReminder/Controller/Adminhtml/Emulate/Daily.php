<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/12/2015
 * Time: 09:47
 */
namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Emulate;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

use Magenest\AbandonedCartReminder\Model\Cron;

class Daily extends Action
{

    protected $_cron;


    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Cron $cron
    ) {
        $this->_context     = $context;
        $this->coreRegistry = $coreRegistry;
        $this->_cron        = $cron;
        parent::__construct($context);

    }//end __construct()


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
       // $this->_cron->daily();

        $this->_cron->hourly();
        return $this->resultRedirectFactory->create()->setPath('abandonedcartreminder/mail/index');

    }//end execute()
}//end class
