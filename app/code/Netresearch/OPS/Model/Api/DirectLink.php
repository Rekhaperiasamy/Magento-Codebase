<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model\Api;

/**
 * OPS payment DirectLink Model
 */
class DirectLink extends \Magento\Framework\DataObject
{
    const MAX_RETRY_COUNT = 3;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment
     */
    protected $oPSPaymentHelper;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * DirectLink constructor.
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param \Netresearch\OPS\Helper\Payment $oPSPaymentHelper
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Psr\Log\LoggerInterface $logger,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->httpClientFactory = $httpClientFactory;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
        $this->oPSHelper = $oPSHelper;
        $this->oPSPaymentHelper = $oPSPaymentHelper;
        $this->oPSConfigFactory = $oPSConfigFactory;
    }

    /**
     * @param $params
     * @param $url
     * @return null|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function call($params, $url)
    {
        $responseBody = null;
        try {
            $http = $this->httpClientFactory->create();
            $http->setConfig([
                'timeout' => 30,
                'verifypeer' => 1,
                'verifyhost' => 2
            ]);
            $http->setUri($url);
            $http->setParameterPost($params);
            $http->setMethod(\Zend_Http_Client::POST);
            $response = $http->request();
            $responseBody = $response->getBody();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Ingenico ePayments server is temporarily not available, please try again later.')
            );
        }

        return $responseBody;
    }

    /**
     * Performs a POST request to the Direct Link Gateway with the given
     * parameters and returns the result parameters as array
     *
     * @param array $requestParams
     * @param $url
     * @param int $storeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function performRequest($requestParams, $url, $storeId = 0)
    {
        $params = $this->getEncodedParametersWithHash(
            //Merge Logic Operation Data with Authentication Data
            array_merge($requestParams, $this->buildAuthenticationParams($storeId)),
            null,
            $storeId
        );
        $responseParams = $this->getResponseParams($params, $url);
        $this->oPSHelper->log(
            __(
                "Direct Link Request/Response in Ingenico ePayments \n\n"
                . "Request: %1\nResponse: %2\nMagento-URL: %3\nAPI-URL: %4",
                serialize($params),
                serialize($responseParams),
                $this->urlBuilder->getCurrentUrl(),
                $url
            )
        );

        $this->checkResponse($responseParams);

        return $responseParams;
    }

    /**
     * @param $params
     * @param null $shaCode
     * @param $storeId
     * @return mixed
     */
    public function getEncodedParametersWithHash($params, $shaCode = null, $storeId = null)
    {
        $hash = $this->oPSPaymentHelper->getSHASign($params, $shaCode, $storeId);
        $params['SHASIGN'] = $this->oPSPaymentHelper->shaCrypt(iconv('iso-8859-1', 'utf-8', $hash));

        return $params;
    }

    /**
     *
     * wraps the request and response handling and repeats request/response
     * if there are errors
     *
     * @param array $params - request params
     * @param string $url - the url for the request
     * @param int $retryCount - current request count
     * @return array|null - null if requests were not successful,
     *                      array containing Ingenico ePayments payment data otherwise
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getResponseParams($params, $url, $retryCount = 0)
    {
        $responseParams = null;
        $responseXml = null;
        if ($retryCount < self::MAX_RETRY_COUNT) {
            try {
                $responseXml = $this->call($params, $url);
                $responseParams = $this->getParamArrFromXmlString($responseXml);
            } catch (\Exception $e) {
                try {
                    $responseParams = $this->getParamArrFromXmlString(utf8_encode($responseXml));
                } catch (\Exception $e) {
                    $ref = '';
                    if (array_key_exists('ORDERID', $params)) {
                        $ref = $params['ORDERID'];
                    } elseif (array_key_exists('PAYID', $params)) {
                        $ref = $params['PAYID'];
                    }
                    $this->oPSHelper->log(
                        'DirectLink::getResponseParams failed: ' .
                        $e->getMessage() . ' current retry count: ' . $retryCount . ' for quote ' . $ref
                    );
                    $responseParams = $this->getResponseParams($params, $url, ++$retryCount);
                }
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('An error occured during the Ingenico ePayments request. Your action could not be executed.')
            );
        }
        return $responseParams;
    }

    /**
     * Return Authentication Params for OPS Call
     *
     * @param int $storeId
     * @return array
     */
    protected function buildAuthenticationParams($storeId = 0)
    {
        return [
            'PSPID' => $this->oPSConfigFactory->create()->getPSPID($storeId),
            'USERID' => $this->oPSConfigFactory->create()->getApiUserId($storeId),
            'PSWD' => $this->oPSConfigFactory->create()->getApiPswd($storeId),
        ];
    }

    /**
     * Parses the XML-String to an array with the result data
     *
     * @param $xmlString
     * @return array
     * @throws \Exception
     */
    public function getParamArrFromXmlString($xmlString)
    {
        try {
            $xml = new \SimpleXMLElement($xmlString);
            foreach ($xml->attributes() as $key => $value) {
                $arrAttr[$key] = (string)$value;
            }
            foreach ($xml->children() as $child) {
                $arrAttr[$child->getName()] = (string) $child;
            }
            return $arrAttr;
        } catch (\Exception $e) {
            $this->logger->debug(
                'Could not convert string to xml in ' . __FILE__ . '::' . __METHOD__ . ': ' . $xmlString
            );
            $this->logger->critical($e);
            throw $e;
        }
    }

    /**
     * Check if the Response from OPS reports Errors
     *
     * @param array $responseParams
     * @return mixed
     * @throws \Exception
     */
    public function checkResponse($responseParams)
    {
        if (false === is_array($responseParams)
            || false === array_key_exists('NCERROR', $responseParams)
            || $responseParams['NCERROR'] > 0
        ) {
            if (empty($responseParams['NCERRORPLUS'])) {
                $responseParams['NCERRORPLUS'] = __('Invalid payment information') .
                    " Errorcode:".$responseParams['NCERROR'];
            }

            //avoid exception if STATUS is set with special values
            if (isset($responseParams['STATUS']) && is_numeric($responseParams['STATUS'])) {
                return;
            }

            throw new \Magento\Framework\Exception\PaymentException(
                __(
                    'An error occured during the Ingenico ePayments request.' .
                    ' Your action could not be executed. Message: "%1".',
                    $responseParams['NCERRORPLUS']
                )
            );
        }
    }
}
