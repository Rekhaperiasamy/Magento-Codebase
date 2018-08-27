<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 22/05/2016
 * Time: 01:59
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

use Magento\Framework\App\ResponseInterface;

class Test extends \Magento\Backend\App\Action
{

    protected $connector;

    protected $mailFac;
    //Magento\Backend\App\Action
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magenest\AbandonedCartReminder\Helper\Connector $connector,
        \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory
    ) {
    
        parent::__construct($context);
        $this->connector = $connector;

        $this->mailFac = $mailFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $this->test_quote_c();

    }

    public function test_send_mail_via_mandrill()
    {
        $mail =$this->mailFac->create()->load(1);
        $this->connector->sendEmail($mail);
    }

    protected function test_get_time_from_mysql()
    {
        // $model = \Magenest\AbandonedCartReminder\Model\MailFactory
        /**
 * @var  $abc  \Magenest\AbandonedCartReminder\Model\AbandonedCart
*/
        $abc = $this->_objectManager->create('Magenest\AbandonedCartReminder\Model\AbandonedCart');
        $rs  =  $abc->getResource();
        $currentTime = $rs->getCurrentTime();
    }

    private function test_quote_time()
    {
        /**
 * @var  $abc  \Magenest\AbandonedCartReminder\Model\AbandonedCart
*/

        $abc = $this->_objectManager->create('Magenest\AbandonedCartReminder\Model\AbandonedCart');
        $rs  =  $abc->getResource();

        $currentTime= $rs->getQuoteTime();
    }
    private function test_quote_c()
    {
        /**
 * @var  $abc  \Magenest\AbandonedCartReminder\Model\AbandonedCart
*/

        $abc = $this->_objectManager->create('Magenest\AbandonedCartReminder\Model\AbandonedCart');
        $rs  =  $abc->getResource();

        $currentTime= $rs->getAbandonedCartForInsertOperation(60);
    }
}
