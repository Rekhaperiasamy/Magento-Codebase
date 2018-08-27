<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Model\Soap;

use SoapClient;
use Zend_Pdf;
use Zend_Pdf_Resource_Extractor;

/**
 * With WS, SOAP response is MTOM, with PDF file in attachment.
 * Basic SoapClient can not handle this response (XML error).
 */
class Client extends SoapClient
{

    /**
     * @var string $filePath
     */
    protected $filePath;

    /**
     * Performs a SOAP request
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param int $one_way [optional]
     * @return string The XML SOAP response.
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $response = parent::__doRequest($request, $location, $action, $version, $one_way);

        if ($this->isMtom($response)) {
            return $this->savePdf($response);
        }

        return $response;
    }

    /**
     * Check if soap response is xop+xml
     *
     * @param string $response
     *
     * @return mixed
     */
    private function isMtom($response)
    {
        return strpos($response, "Content-Type: application/xop+xml") !== false;
    }

    /**
     * Save PDF file from response
     *
     * @param string $response
     *
     * @return mixed
     */
    private function savePdf($response)
    {
        $xopSpilt  = substr($response, 0, stripos($response, "\r\n"));
        $responses = explode($xopSpilt, $response);

        if (!isset($responses[1]) && !isset($responses[2])) {
            return $this->getSoapEnvelope(__('SOAP response invalid'), 'ERROR');
        }

        $message = $this->extractResponse($responses[1]);

        if ($responses[2] == '--') {
            return $message;
        }

        $pdf = array();

        $label = $this->extractResponse($responses[2]);
        $pdf[] = trim($label);

        if (isset($responses[3])) { // CN23
            if ($responses[3] !== '--') {
                $cn23 = $this->extractResponse($responses[3]);
                $pdf[] = trim($cn23);
            }
        }

        file_put_contents($this->filePath, $this->mergePdf($pdf));

        return $this->getSoapEnvelope(
            $this->filePath,
            'SUCCESS',
            $this->extractNode('parcelNumber', $message)
        );
    }

    /**
     * Extract response
     *
     * @param string $data
     * @return string
     */
    private function extractResponse($data)
    {
        return preg_replace("/^(.*\n){4}/", "", $data);
    }

    /**
     * Extract response node
     *
     * @param string $node
     * @param string $data
     * @return string
     */
    private function extractNode($node, $data)
    {
        $result = '';

        if (preg_match('/<' . $node . '>(?P<result>.*)<\/' . $node . '>/', $data, $match)) {
            $result = $match['result'];
        }

        return $result;
    }

    /**
     * Retrieve PDF success envelope
     *
     * @param string $message
     * @param string $type
     * @param string $tracking
     * @return string
     */
    private function getSoapEnvelope($message, $type, $tracking = '')
    {
        return '<?xml version="1.0"?>
            <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                <soap:Header/>
                <soap:Body>
                    <ns2:generateLabelResponse xmlns:ns2="http://sls.ws.coliposte.fr">
                        <return>
                            <messages>
                                <messageContent>' . $message . '</messageContent>
                                <type>' . $type . '</type>
                            </messages>
                            <labelResponse>
                                <parcelNumber>' . $tracking . '</parcelNumber>
                            </labelResponse>
                        </return>
                    </ns2:generateLabelResponse>
                </soap:Body>
            </soap:Envelope>';
    }

    /**
     * Merge PDF in one
     *
     * @param array $files
     * @return string|false
     */
    private function mergePdf($files)
    {
        $merged = new Zend_Pdf();

        foreach ($files as $file) {
            $pdf = Zend_Pdf::parse($file);
            $extractor = new Zend_Pdf_Resource_Extractor();
            foreach ($pdf->pages as $page) {
                $pdfExtract = $extractor->clonePage($page);
                $merged->pages[] = $pdfExtract;
            }
        }

        return $merged->render();
    }

    /**
     * Set File path
     *
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }
}