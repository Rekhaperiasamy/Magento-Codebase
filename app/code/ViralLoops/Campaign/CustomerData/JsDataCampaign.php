<?php
namespace ViralLoops\Campaign\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\CustomerData\SectionSourceInterface;
use ViralLoops\Campaign\Helper\Data as Helper;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class JsDataCampaign implements SectionSourceInterface
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var SessionManagerInterface
     */
    protected $customerSession;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param SessionManagerInterface $customerSession
     * @param Helper $helper
     * @param DateTime $dateTime
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        SessionManagerInterface $customerSession,
        Helper $helper,
        DateTime $dateTime
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->dateTime = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }

        $customer = $this->currentCustomer->getCustomer();
        $viralLoopsHash = $this->getViralLoopsHash($customer);
        $viralLoopsHashDecode = json_decode($viralLoopsHash);

        return [
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'timestamp' => $viralLoopsHashDecode->timestamp,
            'signature' => $viralLoopsHashDecode->hash,
         ];
    }

    /**
     * Add signature to customer session
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     */
    public function getViralLoopsHash($customer)
    {
        if (empty($this->customerSession->getViralLoopsHash())) {
            $email = $customer->getEmail();
            $timestamp = $this->dateTime->gmtTimestamp();
            $apiKey = $this->helper->getApiKey();
            $hash = hash_hmac("sha1", "email='.$email.'&timestamp='.$timestamp.'", $apiKey);
            $this->customerSession->setViralLoopsHash('{ "hash" : "' . $hash . '" , "timestamp": "' . $timestamp . '"}');
        }

        return $this->customerSession->getViralLoopsHash();
    }
}
