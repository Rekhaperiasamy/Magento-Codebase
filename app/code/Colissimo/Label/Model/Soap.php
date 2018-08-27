<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Model;

use Colissimo\Label\Model\Soap\Client as SoapClient;
use Colissimo\Label\Helper\data as LabelHelper;
use Psr\Log\LoggerInterface;
use SoapFault;
use Exception;

/**
 * Class Soap
 */
class Soap
{
    /**
     * @var LabelHelper $labelHelper
     */
    protected $labelHelper;

    /**
     * @var SoapClient $client
     */
    protected $client = null;

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var string $filePath
     */
    protected $filePath;

    /**
     * @param LabelHelper $labelHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        LabelHelper $labelHelper,
        LoggerInterface $logger
    ) {
        $this->labelHelper = $labelHelper;
        $this->logger = $logger;
    }

    /**
     * Execute WS request
     *
     * @param string $method
     * @param array $data
     * @return array
     */
    public function execute($method, $data)
    {
        $result = [
            'error'    => false,
            'response' => false,
        ];

        try {
            $data = array_merge(
                ['contractNumber' => $this->getAccountNumber(), 'password' => $this->getPassword()],
                $data
            );

            $requestData[$method . 'Request'] = $data;

            $request = $this->getClient()->$method($requestData)->return;

            if ($request->messages->type == 'SUCCESS') {
                $result['response'] = $request;
            } else {
                $result['error'] = $request->messages->messageContent;
            }
        } catch (SoapFault $fault) {
            $result['error'] = $fault->getMessage();
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        if ($result['error']) {
            $this->logger->error($result['error']);
        }

        return $result;
    }

    /**
     * Set File path
     *
     * @param $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Retrieve SOAP Client
     *
     * @return SoapClient
     */
    protected function getClient()
    {
        if (is_null($this->client)) {
            if ($this->getApiWsdl()) {
                $this->client = new SoapClient($this->getApiWsdl(), ['exceptions' => true, 'trace' => false]);
                $this->client->setFilePath($this->filePath);
            }
        }

        return $this->client;
    }

    /**
     * Retrieve WSDL Link
     *
     * @return string
     */
    protected function getApiWsdl()
    {
        $config = $this->getApiConfig();

        return $config['wsdl'];
    }

    /**
     * Retrieve API Key
     *
     * @return string
     */
    protected function getAccountNumber()
    {
        $config = $this->getApiConfig();

        return $config['login'];
    }

    /**
     * Retrieve API company
     *
     * @return string
     */
    protected function getPassword()
    {
        $config = $this->getApiConfig();

        return $config['password'];
    }

    /**
     * Retrieve API Config
     *
     * @return array
     */
    protected function getApiConfig()
    {
        return $this->labelHelper->getApiConfig();
    }
}