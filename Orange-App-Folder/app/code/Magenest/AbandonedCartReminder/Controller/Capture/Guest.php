<?php
namespace Magenest\AbandonedCartReminder\Controller\Capture;
class Guest extends \Magento\Framework\App\Action\Action
{

    protected $context;

    protected $checkoutSession;

    protected $logger;

    protected $date;

    protected $guestFactory;
	
	protected $_quoteFactory;

    protected $timezone;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magenest\AbandonedCartReminder\Model\GuestFactory $guestFactory,
		\Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->date = $date;

        $this->timezone = $timezone;
		
		$this->_quoteFactory       = $quoteFactory;

        $this->guestFactory = $guestFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['email'])) {
            $email = $params['email'];

            $quoteIdHash = $params['quote_id'];
			

             $quoteId = $this->checkoutSession->getQuoteId();

           
            //whether the abandoned cart record exist

            $isAbandonedCartExist = false;
			$customerGroupName = '';
			if (isset($params['cutomertype'])) {
				$customerGroupName =  $params['cutomertype'];
			}
            $cartCollection = $this->guestFactory->create()->getCollection()->addFieldToFilter('quote_id', $quoteId);
            

            if ($cartCollection->getSize() > 0) {
                $isAbandonedCartExist = true;
            }
            $quote = $this->_quoteFactory->create();
            if (!$isAbandonedCartExist) {
                $this->guestFactory->create()->setData(['email' => $email, 'quote_id' => $quoteId,'customer_group'=>$customerGroupName])->save();
				$quote->load($quoteId);
				$quote->setCustomerEmail($email)->save();
				if (isset($params['firstname'])) {
					$quote->setCustomerFirstname($params['firstname']);
				}
				if (isset($params['lastname'])) {
					$quote->setCustomerLastname($params['lastname']);
				}
				$quote->save();
            } else {
                $abandonedCart = $cartCollection->getFirstItem();
                $abandonedCart->addData(['email' => $email,'customer_group'=>$customerGroupName])->save();
				$quote->load($quoteId);
				$quote->setCustomerEmail($email);
				if (isset($params['firstname'])) {
					$quote->setCustomerFirstname($params['firstname']);
				}
				if (isset($params['lastname'])) {
					$quote->setCustomerLastname($params['lastname']);
				}
				$quote->save();
            }

        }

    }

}