<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 14/06/2016
 * Time: 09:42
 */

namespace Magenest\AbandonedCartReminder\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Nexmo extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_NEXMO_CONFIG_API_KEY    = 'abandonedcartreminder/nexmo/api_key';
    const XML_PATH_NEXMO_CONFIG_API_SECRET = 'abandonedcartreminder/nexmo/api_secret';
    const XML_PATH_NEXMO_CONFIG_FROM = 'abandonedcartreminder/nexmo/from';

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $zendClientFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Magento\Framework\HTTP\ZendClientFactory $zendClientFactory
     * @param ScopeConfigInterface                      $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\ZendClientFactory $zendClientFactory
    ) {
        $this->zendClientFactory = $zendClientFactory;
        $this->scopeConfig       = $context->getScopeConfig();

    }//end __construct()


    /**
     * @param $sms
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    public function send($sms)
    {
        // "https://rest.nexmo.com/sms/json?api_key=6451aa3c&api_secret=b3127193aa9b4ad3&from=NEXMO&to=84985986898&text=Thank+you+for+buying"
        $apiKey    = $this->scopeConfig->getValue(self::XML_PATH_NEXMO_CONFIG_API_KEY, ScopeInterface::SCOPE_STORE);
        $apiSecret = $this->scopeConfig->getValue(self::XML_PATH_NEXMO_CONFIG_API_SECRET, ScopeInterface::SCOPE_STORE);
        $from = $this->scopeConfig->getValue(self::XML_PATH_NEXMO_CONFIG_FROM, ScopeInterface::SCOPE_STORE);

        $client = $this->zendClientFactory->create();

        $url     = 'https://rest.nexmo.com/sms/json';

        $content = $sms->getContent();
        $to = $sms->getData('recipient_mobile');

        $client->setUri($url);
        $client->setConfig([ 'timeout' => 300]);


        $client->setParameterGet('api_key', $apiKey);
        $client->setParameterGet('api_secret', $apiSecret);
        $client->setParameterGet('from', $from);
        $client->setParameterGet('api_key', $apiKey);
        $client->setParameterGet('to', $to);
        $client->setParameterGet('text', $content);


        $method   = \Zend_Http_Client::GET;
        $response = $client->request($method)->getBody();
        return $response;

    }//end send()
}//end class
